import type { APIRoute } from 'astro';

export const GET: APIRoute = ({ site }) => new Response(
  `User-agent: *\nAllow: /\nDisallow: /operation\nDisallow: /api/\nSitemap: ${new URL('/sitemap.xml', site)}\n`,
  {
    headers: {
      'Content-Type': 'text/plain; charset=utf-8',
      'Cache-Control': 'public, max-age=0, must-revalidate',
      'Netlify-CDN-Cache-Control': 'public, durable, max-age=86400, stale-while-revalidate=604800',
    },
  },
);
