# TheCasinos.com v2

A complete rebuild of TheCasinos.com around its data: 7,493 unique land-based casino URLs, geographic exploration and a manageable online casino Top 10.

## Casino profile enrichment

Land-based profiles are enriched automatically; editors do not have to complete 7,493 records by hand.

- The scheduled `enrich-casinos` Edge Function imports casino entities from Wikidata in restartable batches.
- PostGIS distance and normalized name similarity produce a transparent match score.
- Only unique high-confidence matches are applied automatically. Ambiguous records stay as candidates and never overwrite a profile silently.
- Every accepted field stores its source URL, verification date and confidence trail.
- Verified website, address, phone, opening date and operator data appear on the public profile; known-unreliable legacy operator/hotel fields are no longer presented as facts.
- `/operation` shows source progress, matched records, automatic applications, failures and the next refresh date.

The first source currently covers roughly 941 Wikidata casino entities. The schema is source-neutral so OpenStreetMap, official registers and operator sites can be added in later phases without replacing the evidence trail.

## Visitor tools and training room

- Every land-based casino profile includes a browser-only journey planner. It can use the visitor's current position, show an orientation estimate and hand the exact live route to Google Maps, Apple Maps or Waze. The position is not stored or sent to TheCasinos.com.
- `/training` contains the full European roulette trainer, while casino profiles embed a compact version that uses the same wallet.
- The fictional chip wallet, daily bonus, statistics and recent rounds live in `localStorage`; there is no account, cash-out or server-side balance.
- Roulette settlement logic is isolated in `web/src/lib/roulette.ts` so blackjack, video poker and slots can join the same training economy later without coupling game rules to page markup.

## SEO architecture

- `/casinos` links every country guide.
- Country guides link every indexed city.
- City guides expose all venues through crawlable `/page/:number` routes.
- `sitemap.xml` includes static pages, country/city guides, pagination, casino profiles and review dates.
- Run `SEO_BASE_URL=https://deploy-preview-url npm run audit:seo` from `web/` to validate a deployed build.

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
3. Deploy `supabase/functions/enrich-casinos` with JWT verification disabled as declared in `supabase/config.toml`. The function authenticates scheduled calls with a separate hashed cron token.
4. Store the project URL and a generated cron token in Supabase Vault, write only its SHA-256 hash to `private.casino_enrichment_settings`, then run `private.schedule_casino_enrichment()`.
5. Create the first editor in Supabase Auth.
6. Give that user the admin role in `app_metadata`:

```json
{ "role": "admin" }
```

7. Set the import variables in a trusted local terminal and run:

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
