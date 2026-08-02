import type { APIRoute } from 'astro';
import { getCasinosInView } from '@/lib/data';

export const GET: APIRoute = async ({ url }) => {
  const values = ['west', 'south', 'east', 'north'].map((key) => Number(url.searchParams.get(key)));
  if (values.some((value) => !Number.isFinite(value))) {
    return Response.json({ error: 'Invalid map bounds' }, { status: 400 });
  }
  const [west, south, east, north] = values;
  const casinos = await getCasinosInView({ west, south, east, north });
  return Response.json(casinos, { headers: { 'Cache-Control': 'public, max-age=60', 'Netlify-CDN-Cache-Control': 'public, durable, s-maxage=300, stale-while-revalidate=3600' } });
};
