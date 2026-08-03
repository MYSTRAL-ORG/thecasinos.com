import { gunzip } from 'node:zlib';
import { readFile, readdir } from 'node:fs/promises';
import { basename, dirname, resolve } from 'node:path';
import { promisify } from 'node:util';

const gunzipAsync = promisify(gunzip);

const sourceArgument = process.argv.find((argument) => argument.startsWith('--source='));
const batchArgument = process.argv.find((argument) => argument.startsWith('--emit-sql-batch='));
const batchSizeArgument = process.argv.find((argument) => argument.startsWith('--batch-size='));
const sourcePath = resolve(process.cwd(), sourceArgument?.split('=')[1] || '../data/legacy/casino-details.jsonl.gz');
const batchIndex = batchArgument ? Number(batchArgument.split('=')[1]) : null;
const batchSize = Number(batchSizeArgument?.split('=')[1] || 100);
const emitBatchCount = process.argv.includes('--batch-count');

if (!Number.isInteger(batchSize) || batchSize < 1 || batchSize > 250) {
  throw new Error('--batch-size must be an integer between 1 and 250.');
}

const content = sourcePath.endsWith('.gz') ? await readGzip(sourcePath) : await readFile(sourcePath, 'utf8');
const rows = content.trim().split('\n').filter(Boolean).map((line) => JSON.parse(line));
if (rows.some((row) => row.error)) throw new Error('The legacy detail snapshot contains failed rows.');

if (emitBatchCount) {
  console.log(Math.ceil(rows.length / batchSize));
  process.exit(0);
}

if (batchIndex === null || !Number.isInteger(batchIndex) || batchIndex < 0) {
  throw new Error('Pass --batch-count or a non-negative --emit-sql-batch index.');
}

const batch = rows.slice(batchIndex * batchSize, (batchIndex + 1) * batchSize).map((row) => {
  const [country_slug, city_slug, slug] = row.path.split('/');
  return {
    legacy_id: row.legacy_id,
    country_slug,
    city_slug,
    slug,
    editorial_title: row.editorial_title,
    editorial_paragraphs: row.editorial_paragraphs,
    summary: row.summary,
    games_description: row.games_description,
    fun_facts: row.fun_facts,
    seo_title: row.seo_title,
    seo_description: row.seo_description,
    seo_keywords: row.seo_keywords,
    has_original_image: row.has_original_image,
    legacy_content_source_url: row.source_url,
  };
});
if (!batch.length) throw new Error(`SQL batch ${batchIndex} is outside the import range.`);

const json = JSON.stringify(batch);
const tag = '$thecasinos_details$';
if (json.includes(tag)) throw new Error('Legacy content conflicts with the SQL delimiter.');
process.stdout.write(`
update public.casinos as casino
set editorial_title = source.editorial_title,
    editorial_paragraphs = source.editorial_paragraphs,
    summary = source.summary,
    games_description = source.games_description,
    fun_facts = source.fun_facts,
    seo_title = source.seo_title,
    seo_description = source.seo_description,
    seo_keywords = source.seo_keywords,
    has_original_image = source.has_original_image,
    legacy_content_source_url = source.legacy_content_source_url,
    legacy_content_imported_at = now()
from jsonb_to_recordset(${tag}${json}${tag}::jsonb) as source(
  legacy_id bigint,
  country_slug text,
  city_slug text,
  slug text,
  editorial_title text,
  editorial_paragraphs text[],
  summary text,
  games_description text,
  fun_facts text,
  seo_title text,
  seo_description text,
  seo_keywords text,
  has_original_image boolean,
  legacy_content_source_url text
)
where casino.legacy_id = source.legacy_id
   or (casino.country_slug = source.country_slug and casino.city_slug = source.city_slug and casino.slug = source.slug);
`);

async function readGzip(path) {
  let compressed;
  try {
    compressed = await readFile(path);
  } catch (error) {
    if (error?.code !== 'ENOENT') throw error;
    const directory = dirname(path);
    const prefix = `${basename(path)}.part-`;
    const parts = (await readdir(directory)).filter((name) => name.startsWith(prefix)).sort();
    if (!parts.length) throw error;
    compressed = Buffer.concat(await Promise.all(parts.map((name) => readFile(resolve(directory, name)))));
  }
  return (await gunzipAsync(compressed)).toString('utf8');
}
