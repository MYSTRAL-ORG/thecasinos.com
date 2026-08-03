# TheCasinos.com v2

A complete rebuild of TheCasinos.com around its data: 7,493 unique land-based casino URLs, geographic exploration and a manageable online casino Top 10.

## Stack

- Astro 7 with server-rendered routes and selective client-side JavaScript
- Supabase Postgres, PostGIS, Auth and Row Level Security
- Netlify SSR adapter and CDN configuration
- Leaflet for viewport-based geographic exploration

The former Laravel runtime is intentionally removed. The legacy GeoJSON remains in `data/legacy/casinos.geojson`, the 7,493 recovered editorial profiles are preserved in the ordered `data/legacy/casino-details.jsonl.gz.part-*` archive chunks, and the original brand logo is retained in `web/public/logo.png`.

## Local setup

Requirements: Node.js 22.12+ (Node 24 is used on Netlify) and a Supabase project.

```bash
cd web
cp .env.example .env
npm install
npm run dev
```

For a public-data fallback preview, the site also runs without Supabase variables. Authentication and editing are disabled until the project URL and anonymous key are configured.

## Supabase setup

1. Link the repository to the intended Supabase project.
2. Apply the SQL files in `supabase/migrations` in timestamp order.
3. Create the first editor in Supabase Auth.
4. Give that user the admin role in `app_metadata`:

```json
{ "role": "admin" }
```

5. Set the import variables in a trusted local terminal and run:

```bash
cd web
npm run audit:legacy
npm run import:legacy
npm run import:legacy-details -- --batch-count
```

`SUPABASE_SERVICE_ROLE_KEY` is used only by the local import script. It must never be prefixed with `PUBLIC_`, committed, or exposed in browser/Netlify public configuration.

The import batches upserts by legacy ID and merges 34 duplicate records that previously shared the same public URL. The resulting public directory has 7,493 unique casino routes.

`import:legacy-details` can emit idempotent SQL batches from the compressed editorial snapshot. It matches by legacy ID and public path so records collapsed from duplicate legacy URLs still receive their complete content.

## Netlify

The root `netlify.toml` sets `web` as the base directory, builds the Astro SSR output, configures security headers and excludes `/operation` from caching and indexing.

Configure only these public variables for the web application:

```text
PUBLIC_SUPABASE_URL
PUBLIC_SUPABASE_ANON_KEY
```

Do not configure a service-role key in Netlify. All browser-side administration uses the authenticated user token and is enforced by RLS.

The 2,020 recoverable original casino photos and the eight historical fallback visuals live under `web/public/media`. Netlify Image CDN resizes these local sources on demand, so the rebuilt site does not depend on the legacy domain or on anonymous Storage access.

## Quality checks

```bash
cd web
npm run check
npm run build
npm run audit:legacy
```

The main preserved URL shapes are:

- `/{country}`
- `/{country}/{city}`
- `/{country}/{city}/{casino-slug}`
- `/online`
- `/online/{legacy-slug}`

## Security note

The previous repository version contained a Google service-account key, a PostgreSQL dump with account tables, and an `.htpasswd` file. They have been removed from the new tree, but Git history still contains them. Revoke/rotate those credentials and rewrite the repository history before treating the repository as clean.
