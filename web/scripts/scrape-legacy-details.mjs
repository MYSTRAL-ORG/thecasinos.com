import { createWriteStream } from 'node:fs';
import { readFile } from 'node:fs/promises';
import { resolve } from 'node:path';
import { once } from 'node:events';

const sourcePath = resolve(process.cwd(), process.env.LEGACY_GEOJSON_PATH || '../data/legacy/casinos.geojson');
const outputArgument = process.argv.find((argument) => argument.startsWith('--output='));
const concurrencyArgument = process.argv.find((argument) => argument.startsWith('--concurrency='));
const limitArgument = process.argv.find((argument) => argument.startsWith('--limit='));
const outputPath = resolve(process.cwd(), outputArgument?.split('=')[1] || '../data/legacy/casino-details.jsonl');
const concurrency = Number(concurrencyArgument?.split('=')[1] || 40);
const limit = Number(limitArgument?.split('=')[1] || Number.POSITIVE_INFINITY);
const resume = process.argv.includes('--resume');

if (!Number.isInteger(concurrency) || concurrency < 1 || concurrency > 100) {
  throw new Error('--concurrency must be an integer between 1 and 100.');
}
if (!(Number.isFinite(limit) || limit === Number.POSITIVE_INFINITY) || limit < 1) {
  throw new Error('--limit must be a positive number.');
}

const source = JSON.parse(await readFile(sourcePath, 'utf8'));
const rowsByPath = new Map();
for (const feature of source.features ?? []) {
  const properties = feature.properties ?? {};
  const path = `${properties.countrytitle}/${properties.citytitle}/${properties.slug}`;
  if (!rowsByPath.has(path)) {
    rowsByPath.set(path, {
      legacy_id: Number(feature.id),
      path,
      image_name: String(properties.imgurl ?? ''),
      has_original_image: Boolean(properties.originalimg),
    });
  }
}

const completedIds = new Set();
if (resume) {
  const existing = await readFile(outputPath, 'utf8').catch(() => '');
  for (const line of existing.split('\n').filter(Boolean)) {
    try {
      const row = JSON.parse(line);
      if (!row.error && row.legacy_id) completedIds.add(Number(row.legacy_id));
    } catch {
      // A final interrupted line is ignored; all completed JSON lines stay reusable.
    }
  }
}
const targets = [...rowsByPath.values()].filter((target) => !completedIds.has(target.legacy_id)).slice(0, limit);
const output = createWriteStream(outputPath, { flags: resume ? 'a' : 'w' });
output.setMaxListeners(concurrency + 10);
if (resume) console.log(`Resuming with ${completedIds.size} completed rows; ${targets.length} remain.`);
let nextIndex = 0;
let completed = 0;
let failures = 0;

async function worker() {
  while (nextIndex < targets.length) {
    const target = targets[nextIndex++];
    try {
      const detail = await scrape(target);
      if (!output.write(`${JSON.stringify(detail)}\n`)) await once(output, 'drain');
    } catch (error) {
      failures += 1;
      const failure = { legacy_id: target.legacy_id, path: target.path, error: error instanceof Error ? error.message : String(error) };
      if (!output.write(`${JSON.stringify(failure)}\n`)) await once(output, 'drain');
    }
    completed += 1;
    if (completed % 100 === 0 || completed === targets.length) {
      console.log(`Scraped ${completed} / ${targets.length} (${failures} failures)`);
    }
  }
}

await Promise.all(Array.from({ length: Math.min(concurrency, targets.length) }, () => worker()));
output.end();
await once(output, 'finish');

if (failures) process.exitCode = 1;

async function scrape(target) {
  const url = `https://www.thecasinos.com/${target.path}`;
  let lastError;
  for (let attempt = 1; attempt <= 3; attempt += 1) {
    try {
      const response = await fetch(url, {
        headers: { 'User-Agent': 'TheCasinos migration/2.0 (+https://www.thecasinos.com)' },
        signal: AbortSignal.timeout(30_000),
      });
      if (!response.ok) throw new Error(`HTTP ${response.status}`);
      return parsePage(await response.text(), target, url);
    } catch (error) {
      lastError = error;
      if (attempt < 3) await new Promise((resolveWait) => setTimeout(resolveWait, attempt * 750));
    }
  }
  throw lastError;
}

function parsePage(html, target, sourceUrl) {
  const articleHtml = match(html, /<div class="content-casino[^>]*>([\s\S]*?)<\/div>/i);
  const paragraphs = [...articleHtml.matchAll(/<p\b[^>]*>([\s\S]*?)<\/p>/gi)]
    .map((result) => toText(result[1]))
    .filter(Boolean);
  const title = toText(match(html, /<h2 class="h2">([\s\S]*?)<\/h2>/i));
  const seoTitle = toText(match(html, /<title>([\s\S]*?)<\/title>/i));
  const seoDescription = decodeAttribute(match(html, /<meta\s+name="description"\s+content="([\s\S]*?)"\s*\/?>/i));
  const seoKeywords = decodeAttribute(match(html, /<meta\s+name="keywords"\s+content="([\s\S]*?)"\s*\/?>/i));

  if (!title || !paragraphs.length) throw new Error('Legacy editorial content was not found.');

  return {
    legacy_id: target.legacy_id,
    path: target.path,
    editorial_title: title,
    editorial_paragraphs: paragraphs,
    summary: sectionText(html, 'sumup'),
    games_description: sectionText(html, 'games'),
    fun_facts: sectionText(html, 'funfacts'),
    seo_title: seoTitle || title,
    seo_description: seoDescription || null,
    seo_keywords: seoKeywords || null,
    image_name: target.image_name || null,
    has_original_image: target.has_original_image,
    source_url: sourceUrl,
  };
}

function sectionText(html, id) {
  const block = match(html, new RegExp(`<div class="casino-block" id="${id}">([\\s\\S]*?)(?=<div class="casino-block"|<\\/aside>|<\\/div>\\s*<\\/div>)`, 'i'));
  const paragraph = match(block, /<p\b[^>]*>([\s\S]*?)<\/p>/i);
  return toText(paragraph) || null;
}

function match(value, pattern) {
  return value.match(pattern)?.[1] ?? '';
}

function decodeAttribute(value) {
  return decodeEntities(value).replace(/\s+/g, ' ').trim();
}

function toText(value) {
  return decodeEntities(value.replace(/<br\s*\/?>/gi, ' ').replace(/<[^>]+>/g, ' ')).replace(/\s+/g, ' ').trim();
}

function decodeEntities(value) {
  const named = { amp: '&', apos: "'", gt: '>', lt: '<', nbsp: ' ', quot: '"' };
  return value.replace(/&(#x?[0-9a-f]+|[a-z]+);/gi, (entity, code) => {
    if (code[0] === '#') {
      const hex = code[1]?.toLowerCase() === 'x';
      const point = Number.parseInt(code.slice(hex ? 2 : 1), hex ? 16 : 10);
      return Number.isFinite(point) ? String.fromCodePoint(point) : entity;
    }
    return named[code.toLowerCase()] ?? entity;
  });
}
