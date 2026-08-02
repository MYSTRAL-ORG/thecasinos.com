import { readFile } from 'node:fs/promises';
import { resolve } from 'node:path';

const sourcePath = resolve(process.cwd(), process.env.LEGACY_GEOJSON_PATH || '../data/legacy/casinos.geojson');
const source = JSON.parse(await readFile(sourcePath, 'utf8'));
const paths = new Map();
const issues = [];

for (const feature of source.features ?? []) {
  const properties = feature.properties ?? {};
  const path = `/${properties.countrytitle}/${properties.citytitle}/${properties.slug}`;
  if ([properties.countrytitle, properties.citytitle, properties.slug].some((value) => !value)) issues.push({ id: feature.id, issue: 'missing route segment' });
  if (paths.has(path)) issues.push({ id: feature.id, issue: `duplicate route with ${paths.get(path)}`, path });
  paths.set(path, feature.id);
  const coordinates = feature.geometry?.coordinates;
  if (!Array.isArray(coordinates) || coordinates.length < 2 || !coordinates.every(Number.isFinite)) issues.push({ id: feature.id, issue: 'invalid coordinates' });
}

console.log(JSON.stringify({ features: source.features?.length ?? 0, uniqueRoutes: paths.size, issueCount: issues.length, sampleIssues: issues.slice(0, 25) }, null, 2));
if (issues.some((issue) => issue.issue === 'missing route segment')) process.exitCode = 1;
