import { readFile } from 'node:fs/promises';
import { resolve } from 'node:path';
import { createClient } from '@supabase/supabase-js';

const sourcePath = resolve(process.cwd(), process.env.LEGACY_GEOJSON_PATH || '../data/legacy/casinos.geojson');
const supabaseUrl = process.env.PUBLIC_SUPABASE_URL || process.env.SUPABASE_URL;
const serviceRoleKey = process.env.SUPABASE_SERVICE_ROLE_KEY;
const dryRun = process.argv.includes('--dry-run');
const sqlBatchArgument = process.argv.find((argument) => argument.startsWith('--emit-sql-batch='));
const batchSizeArgument = process.argv.find((argument) => argument.startsWith('--batch-size='));
const emitBatchCount = process.argv.includes('--batch-count');
const sqlBatchIndex = sqlBatchArgument ? Number(sqlBatchArgument.split('=')[1]) : null;
const batchSize = batchSizeArgument ? Number(batchSizeArgument.split('=')[1]) : 250;

if (!Number.isInteger(batchSize) || batchSize < 1 || batchSize > 1000) {
  throw new Error('--batch-size must be an integer between 1 and 1000.');
}

if (!dryRun && sqlBatchIndex === null && !emitBatchCount && (!supabaseUrl || !serviceRoleKey)) {
  throw new Error('Set PUBLIC_SUPABASE_URL (or SUPABASE_URL) and SUPABASE_SERVICE_ROLE_KEY before importing.');
}

const source = JSON.parse(await readFile(sourcePath, 'utf8'));
if (source?.type !== 'FeatureCollection' || !Array.isArray(source.features)) {
  throw new Error(`Expected a GeoJSON FeatureCollection at ${sourcePath}`);
}

const candidates = source.features.map((feature) => {
  const properties = feature.properties ?? {};
  const [longitude, latitude] = normalizeCoordinates(feature.geometry?.coordinates);
  return {
    legacy_id: Number(feature.id),
    name: String(properties.name ?? '').trim(),
    slug: String(properties.slug ?? '').trim(),
    country_name: String(properties.countryname ?? '').trim(),
    country_slug: String(properties.countrytitle ?? '').trim(),
    state_name: properties.statename || null,
    city_name: String(properties.cityname ?? '').trim(),
    city_slug: String(properties.citytitle ?? '').trim(),
    short_description: properties.shortdesc || null,
    description: properties.longdesc || null,
    opened_on: properties.opened || null,
    gaming_machines: finiteOrNull(properties.gaming_machines),
    poker_tables: finiteOrNull(properties.poker_tables),
    table_games: finiteOrNull(properties.table_games),
    square_footage: finiteOrNull(properties.square_footage ?? properties.squarefootage),
    hotel_name: properties.hotel_name || null,
    owner_name: properties.owners || null,
    always_open: booleanOrNull(properties.always_open),
    has_sportsbook: Boolean(properties.cat_sportsbook),
    has_horse_racing: Boolean(properties.cat_horseracing),
    has_simulcasting: Boolean(properties.cat_simulcasting),
    has_offtrack_betting: Boolean(properties.cat_offtrack),
    has_greyhounds: Boolean(properties.cat_greyhounds),
    has_bingo: Boolean(properties.cat_bingo),
    has_slots: Boolean(properties.cat_slotmachines),
    has_table_games: Boolean(properties.cat_tablegames),
    longitude,
    latitude,
    legacy_image_name: properties.imgurl || null,
    has_original_image: Boolean(properties.originalimg),
    published: true,
  };
}).filter((row) => row.legacy_id && row.name && row.slug && row.country_name && row.country_slug && row.city_name && row.city_slug);

// The legacy dataset contains multiple records behind 34 identical public URLs.
// Because those records were never independently addressable, retain the most
// complete row and merge any missing values from its duplicate.
const rowsByPath = new Map();
for (const candidate of candidates) {
  const path = `${candidate.country_slug}/${candidate.city_slug}/${candidate.slug}`;
  const existing = rowsByPath.get(path);
  if (!existing) {
    rowsByPath.set(path, candidate);
    continue;
  }
  const preferred = completenessScore(candidate) > completenessScore(existing) ? candidate : existing;
  const alternate = preferred === candidate ? existing : candidate;
  rowsByPath.set(path, mergeMissing(preferred, alternate));
}
const rows = [...rowsByPath.values()];

const collapsedCount = candidates.length - rows.length;
const invalidCount = source.features.length - candidates.length;

if (emitBatchCount) {
  console.log(Math.ceil(rows.length / batchSize));
  process.exit(0);
}

if (sqlBatchIndex !== null) {
  if (!Number.isInteger(sqlBatchIndex) || sqlBatchIndex < 0) {
    throw new Error('--emit-sql-batch must be a non-negative integer.');
  }
  const batch = rows.slice(sqlBatchIndex * batchSize, (sqlBatchIndex + 1) * batchSize);
  if (!batch.length) throw new Error(`SQL batch ${sqlBatchIndex} is outside the import range.`);
  await new Promise((resolveWrite, rejectWrite) => {
    process.stdout.write(`${buildUpsertSql(batch)}\n`, (error) => error ? rejectWrite(error) : resolveWrite());
  });
  process.exit(0);
}

if (invalidCount) console.warn(`Skipping ${invalidCount} incomplete legacy records.`);
if (collapsedCount) console.warn(`Merged ${collapsedCount} records that shared an existing public URL.`);

if (dryRun) {
  console.log(JSON.stringify({ sourceRecords: source.features.length, importRows: rows.length, mergedDuplicateUrls: collapsedCount, skippedRows: invalidCount }, null, 2));
  process.exit(0);
}

const client = createClient(supabaseUrl, serviceRoleKey, { auth: { persistSession: false, autoRefreshToken: false } });
for (let index = 0; index < rows.length; index += batchSize) {
  const batch = rows.slice(index, index + batchSize);
  const { error } = await client.from('casinos').upsert(batch, { onConflict: 'legacy_id' });
  if (error) throw new Error(`Import failed at row ${index + 1}: ${error.message}`);
  console.log(`Imported ${Math.min(index + batch.length, rows.length)} / ${rows.length}`);
}
console.log(`Legacy import complete: ${rows.length} casino records.`);

function finiteOrNull(value) {
  const number = Number(value);
  return Number.isFinite(number) && number >= 0 ? number : null;
}

function booleanOrNull(value) {
  return value == null ? null : Boolean(value);
}

function normalizeCoordinates(coordinates) {
  let [longitude, latitude] = Array.isArray(coordinates) ? coordinates.map(Number) : [Number.NaN, Number.NaN];
  if (Number.isFinite(longitude) && Number.isFinite(latitude) && Math.abs(latitude) > 90 && Math.abs(longitude) <= 90) {
    [longitude, latitude] = [latitude, longitude];
  }
  return [
    Number.isFinite(longitude) && longitude >= -180 && longitude <= 180 ? longitude : null,
    Number.isFinite(latitude) && latitude >= -90 && latitude <= 90 ? latitude : null,
  ];
}

function completenessScore(row) {
  return Object.values(row).filter((value) => value !== null && value !== '' && value !== false && value !== 0).length;
}

function mergeMissing(preferred, alternate) {
  return Object.fromEntries(Object.entries(preferred).map(([key, value]) => [key, value == null || value === '' ? alternate[key] ?? value : value]));
}

function buildUpsertSql(batch) {
  const columns = Object.keys(batch[0]);
  const json = JSON.stringify(batch);
  const dollarTag = '$thecasinos_import$';
  if (json.includes(dollarTag)) throw new Error('Legacy content conflicts with the SQL import delimiter.');
  const columnList = columns.join(', ');
  const updates = columns
    .filter((column) => column !== 'legacy_id')
    .map((column) => `${column} = excluded.${column}`)
    .join(',\n  ');
  return `insert into public.casinos (${columnList})\nselect ${columnList}\nfrom jsonb_populate_recordset(null::public.casinos, ${dollarTag}${json}${dollarTag}::jsonb)\non conflict (legacy_id) do update set\n  ${updates};`;
}
