import type { APIRoute } from 'astro';
import { purgeCache } from '@netlify/functions';

export const prerender = false;

export const POST: APIRoute = async ({ request }) => {
  const expectedSecret = import.meta.env.CACHE_PURGE_SECRET;
  const authorization = request.headers.get('authorization');

  if (!expectedSecret || authorization !== `Bearer ${expectedSecret}`) {
    return new Response('Unauthorized.', {
      status: 401,
      headers: { 'Cache-Control': 'no-store' },
    });
  }

  await purgeCache();

  return new Response('Cache purge accepted.', {
    status: 202,
    headers: {
      'Cache-Control': 'no-store',
      'Content-Type': 'text/plain; charset=utf-8',
    },
  });
};
