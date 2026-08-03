const baseUrl = new URL(process.env.SEO_BASE_URL || 'https://www.thecasinos.com');
const canonicalOrigin = new URL(process.env.SEO_CANONICAL_ORIGIN || 'https://www.thecasinos.com');
const minimumSitemapUrls = Number(process.env.SEO_MIN_SITEMAP_URLS || 10_000);

const failures = [];
const checks = [];

function assert(condition, message) {
  if (condition) checks.push(message);
  else failures.push(message);
}

async function request(path, options = {}) {
  const response = await fetch(new URL(path, baseUrl), { redirect: 'manual', ...options });
  return { response, body: await response.text() };
}

function htmlValue(html, pattern) {
  return html.match(pattern)?.[1]?.trim() || '';
}

function canonicalFor(path) {
  const pathname = path === '/' ? '/' : path.replace(/\/+$/, '');
  return new URL(pathname, canonicalOrigin).toString();
}

async function auditIndexablePage(path) {
  const { response, body } = await request(path);
  assert(response.status === 200, `${path} returns HTTP 200`);
  const title = htmlValue(body, /<title>([^<]+)<\/title>/i);
  const description = htmlValue(body, /<meta\s+name="description"\s+content="([^"]+)"/i);
  const robots = htmlValue(body, /<meta\s+name="robots"\s+content="([^"]+)"/i);
  const canonical = htmlValue(body, /<link\s+rel="canonical"\s+href="([^"]+)"/i);
  assert(title.length >= 15 && title.length <= 90, `${path} has a usable title`);
  assert(description.length >= 50 && description.length <= 180, `${path} has a usable description`);
  assert(robots.startsWith('index,follow'), `${path} is indexable`);
  assert(canonical === canonicalFor(path), `${path} has a self-referencing production canonical`);
  assert((body.match(/type="application\/ld\+json"/g) || []).length >= 1, `${path} exposes JSON-LD`);
}

const [{ response: sitemapResponse, body: sitemap }, { response: robotsResponse, body: robots }] = await Promise.all([
  request('/sitemap.xml'),
  request('/robots.txt'),
]);

assert(sitemapResponse.status === 200, '/sitemap.xml returns HTTP 200');
assert(sitemapResponse.headers.get('content-type')?.includes('application/xml'), '/sitemap.xml uses an XML content type');
assert(robotsResponse.status === 200, '/robots.txt returns HTTP 200');
assert(robots.includes(`Sitemap: ${new URL('/sitemap.xml', canonicalOrigin)}`), 'robots.txt declares the canonical sitemap');
assert(robots.includes('Disallow: /operation'), 'robots.txt blocks private operations');
assert(robots.includes('Disallow: /api/'), 'robots.txt blocks API crawling');

const sitemapUrls = [...sitemap.matchAll(/<loc>([^<]+)<\/loc>/g)].map((match) => match[1]);
const uniqueSitemapUrls = new Set(sitemapUrls);
assert(sitemapUrls.length >= minimumSitemapUrls, `sitemap contains at least ${minimumSitemapUrls.toLocaleString('en-US')} URLs`);
assert(uniqueSitemapUrls.size === sitemapUrls.length, 'sitemap contains no duplicate URLs');
assert(uniqueSitemapUrls.has(new URL('/casinos', canonicalOrigin).toString()), 'sitemap includes the world directory');
assert(uniqueSitemapUrls.has(new URL('/united-states', canonicalOrigin).toString()), 'sitemap includes country guides');
assert(uniqueSitemapUrls.has(new URL('/united-states/las-vegas', canonicalOrigin).toString()), 'sitemap includes city guides');
assert(uniqueSitemapUrls.has(new URL('/united-states/las-vegas/page/2', canonicalOrigin).toString()), 'sitemap includes crawlable city pagination');
assert(!sitemapUrls.some((url) => /\/(operation|api|search)(\/|\?|$)/.test(new URL(url).pathname)), 'sitemap excludes private, API and search URLs');

const corePaths = ['/', '/casinos', '/online', '/united-states', '/united-states/las-vegas', '/united-states/las-vegas/page/2'];
const detailPaths = sitemapUrls
  .map((url) => new URL(url).pathname)
  .filter((path) => path.split('/').filter(Boolean).length === 3 && !path.startsWith('/online/'))
  .slice(0, 6);
await Promise.all([...corePaths, ...detailPaths].map(auditIndexablePage));

const [{ response: missingResponse, body: missingBody }, { response: searchResponse, body: searchBody }, pageOneRedirect] = await Promise.all([
  request('/directory-page-that-does-not-exist'),
  request('/search?q=casino'),
  request('/united-states/las-vegas/page/1'),
]);
assert(missingResponse.status === 404, 'missing directory URLs return HTTP 404');
assert(/content="noindex,(?:follow|nofollow)"/.test(missingBody), '404 pages are noindex');
assert(searchResponse.status === 200 && /content="noindex,follow"/.test(searchBody), 'internal search results are noindex,follow');
assert(pageOneRedirect.response.status === 301 && pageOneRedirect.response.headers.get('location') === '/united-states/las-vegas', 'page 1 pagination redirects permanently to the city canonical');

if (failures.length) {
  console.error(`SEO audit failed (${failures.length}/${failures.length + checks.length} checks):`);
  for (const failure of failures) console.error(`- ${failure}`);
  process.exit(1);
}

console.log(`SEO audit passed: ${checks.length} checks, ${sitemapUrls.length.toLocaleString('en-US')} canonical URLs.`);
