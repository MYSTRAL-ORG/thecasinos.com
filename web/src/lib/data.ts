import { fallbackCasinos, fallbackDestinations, fallbackOnlineCasinos } from './fallback-data';
import { hasSupabase, supabase } from './supabase';
import type { Casino, Destination, OnlineCasino } from './types';

const casinoFields = 'id,legacy_id,name,slug,country_name,country_slug,city_name,city_slug,state_name,short_description,description,editorial_title,editorial_paragraphs,summary,games_description,fun_facts,seo_title,seo_description,seo_keywords,opened_on,gaming_machines,poker_tables,table_games,square_footage,hotel_name,owner_name,always_open,has_sportsbook,has_bingo,has_slots,has_table_games,longitude,latitude,legacy_image_name,has_original_image,published';

export async function getSiteStats() {
  if (!supabase) return { casinos: 7527, countries: 157, cities: 3145 };
  const [casinos, destinations] = await Promise.all([
    supabase.from('casinos').select('id', { count: 'exact', head: true }).eq('published', true),
    supabase.rpc('directory_stats'),
  ]);
  if (casinos.error || destinations.error) return { casinos: 7527, countries: 157, cities: 3145 };
  const stats = destinations.data?.[0];
  return {
    casinos: casinos.count ?? 7527,
    countries: Number(stats?.countries ?? 157),
    cities: Number(stats?.cities ?? 3145),
  };
}

export async function getFeaturedCasinos(limit = 5): Promise<Casino[]> {
  if (!supabase) return fallbackCasinos.slice(0, limit);
  const { data, error } = await supabase
    .from('casinos')
    .select(casinoFields)
    .eq('published', true)
    .order('featured', { ascending: false })
    .order('name')
    .limit(limit);
  return error || !data?.length ? fallbackCasinos.slice(0, limit) : (data as Casino[]);
}

export async function getPopularDestinations(limit = 6): Promise<Destination[]> {
  if (!supabase) return fallbackDestinations.slice(0, limit);
  const { data, error } = await supabase.rpc('popular_destinations', { result_limit: limit });
  return error || !data?.length ? fallbackDestinations.slice(0, limit) : (data as Destination[]);
}

export async function getOnlineCasinos(options: { admin?: boolean } = {}): Promise<OnlineCasino[]> {
  const fallback = options.admin
    ? fallbackOnlineCasinos
    : fallbackOnlineCasinos.filter((casino) => casino.active && casino.published).sort((a, b) => Number(a.position ?? 99) - Number(b.position ?? 99)).slice(0, 10);
  if (!supabase) return fallback;
  let query = supabase.from('online_casinos').select('*').order('position', { ascending: true, nullsFirst: false });
  if (!options.admin) query = query.eq('active', true).eq('published', true).not('position', 'is', null).limit(10);
  const { data, error } = await query;
  return error || !data?.length ? fallback : (data as OnlineCasino[]);
}

export async function getOnlineCasino(slug: string): Promise<OnlineCasino | null> {
  if (supabase) {
    const { data, error } = await supabase.from('online_casinos').select('*').eq('slug', slug).eq('published', true).maybeSingle();
    if (!error && data) return data as OnlineCasino;
  }
  return fallbackOnlineCasinos.find((casino) => casino.published && casino.slug.toLowerCase() === slug.toLowerCase()) ?? null;
}

export async function getCasinoByPath(country: string, city: string, slug: string): Promise<Casino | null> {
  if (supabase) {
    const { data, error } = await supabase
      .from('casinos')
      .select(casinoFields)
      .eq('country_slug', country)
      .eq('city_slug', city)
      .eq('slug', slug)
      .eq('published', true)
      .maybeSingle();
    if (!error && data) return data as Casino;
  }
  return fallbackCasinos.find((casino) => casino.country_slug === country && casino.city_slug === city && casino.slug === slug) ?? null;
}

export async function getCasinosByLocation(country: string, city?: string, limit = 60): Promise<Casino[]> {
  if (supabase) {
    let query = supabase.from('casinos').select(casinoFields).eq('country_slug', country).eq('published', true).order('name').limit(limit);
    if (city) query = query.eq('city_slug', city);
    const { data, error } = await query;
    if (!error && data?.length) return data as Casino[];
  }
  return fallbackCasinos.filter((casino) => casino.country_slug === country && (!city || casino.city_slug === city));
}

export async function searchCasinos(term: string, limit = 30): Promise<Casino[]> {
  const query = term.trim();
  if (!query) return [];
  if (supabase) {
    const { data, error } = await supabase.rpc('search_casinos', { search_term: query, result_limit: limit });
    if (!error && data) return data as Casino[];
  }
  const normalized = query.toLocaleLowerCase();
  return fallbackCasinos.filter((casino) => [casino.name, casino.city_name, casino.country_name].some((value) => value.toLocaleLowerCase().includes(normalized))).slice(0, limit);
}

export async function getCasinosInView(bounds: { west: number; south: number; east: number; north: number }, limit = 250): Promise<Casino[]> {
  if (supabase) {
    const { data, error } = await supabase.rpc('casinos_in_view', { ...bounds, result_limit: limit });
    if (!error && data) return data as Casino[];
  }
  return fallbackCasinos.filter((casino) => {
    if (casino.longitude == null || casino.latitude == null) return false;
    return casino.longitude >= bounds.west && casino.longitude <= bounds.east && casino.latitude >= bounds.south && casino.latitude <= bounds.north;
  });
}

export async function getAllPublicUrls(): Promise<string[]> {
  if (!supabase || !hasSupabase) {
    return [
      ...fallbackCasinos.map((casino) => `/${casino.country_slug}/${casino.city_slug}/${casino.slug}`),
      ...fallbackOnlineCasinos.filter((casino) => casino.published).map((casino) => `/online/${casino.slug}`),
    ];
  }
  const urls: string[] = [];
  for (let from = 0; ; from += 1000) {
    const { data, error } = await supabase.from('casinos').select('country_slug,city_slug,slug').eq('published', true).range(from, from + 999);
    if (error || !data?.length) break;
    urls.push(...data.map((row) => `/${row.country_slug}/${row.city_slug}/${row.slug}`));
    if (data.length < 1000) break;
  }
  const { data: online } = await supabase.from('online_casinos').select('slug').eq('published', true);
  if (online) urls.push(...online.map((row) => `/online/${row.slug}`));
  return urls;
}
