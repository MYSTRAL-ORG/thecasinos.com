import type { APIRoute } from 'astro';
import { getAllPublicUrls } from '@/lib/data';
import type { PublicUrl } from '@/lib/types';

const escapeXml = (value: string) => value.replace(/[<>&'\"]/g, (character) => ({ '<': '&lt;', '>': '&gt;', '&': '&amp;', "'": '&apos;', '"': '&quot;' })[character] ?? character);

export const GET: APIRoute = async ({ site }) => {
  try {
    const dynamicUrls = await getAllPublicUrls();
    const urls: PublicUrl[] = [
      { path: '/' },
      { path: '/casinos' },
      { path: '/online' },
      { path: '/about' },
      { path: '/terms' },
      { path: '/policy' },
      ...dynamicUrls,
    ];
    const uniqueUrls = [...new Map(urls.map((url) => [url.path, url])).values()];
    const entries = uniqueUrls.map(({ path, lastModified }) => {
      const location = `<loc>${escapeXml(new URL(path, site).toString())}</loc>`;
      const lastmod = lastModified ? `<lastmod>${escapeXml(new Date(lastModified).toISOString())}</lastmod>` : '';
      return `  <url>${location}${lastmod}</url>`;
    });
    const body = `<?xml version="1.0" encoding="UTF-8"?>\n<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n${entries.join('\n')}\n</urlset>`;
    return new Response(body, {
      headers: {
        'Content-Type': 'application/xml; charset=utf-8',
        'Cache-Control': 'public, max-age=0, must-revalidate',
        'Netlify-CDN-Cache-Control': 'public, durable, max-age=3600, stale-while-revalidate=86400',
      },
    });
  } catch {
    return new Response('Sitemap temporarily unavailable', {
      status: 503,
      headers: {
        'Content-Type': 'text/plain; charset=utf-8',
        'Cache-Control': 'no-store',
        'Retry-After': '300',
        'X-Robots-Tag': 'noindex',
      },
    });
  }
};
