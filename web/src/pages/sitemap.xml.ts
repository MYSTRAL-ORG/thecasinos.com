import type { APIRoute } from 'astro';
import { getAllPublicUrls } from '@/lib/data';

const escapeXml = (value: string) => value.replace(/[<>&'\"]/g, (character) => ({ '<': '&lt;', '>': '&gt;', '&': '&amp;', "'": '&apos;', '"': '&quot;' })[character] ?? character);

export const GET: APIRoute = async ({ site }) => {
  const dynamicUrls = await getAllPublicUrls();
  const urls = ['/', '/online', '/about', '/terms', '/policy', ...dynamicUrls];
  const body = `<?xml version="1.0" encoding="UTF-8"?>\n<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n${urls.map((path) => `  <url><loc>${escapeXml(new URL(path, site).toString())}</loc></url>`).join('\n')}\n</urlset>`;
  return new Response(body, { headers: { 'Content-Type': 'application/xml; charset=utf-8', 'Netlify-CDN-Cache-Control': 'public, durable, s-maxage=3600, stale-while-revalidate=86400' } });
};
