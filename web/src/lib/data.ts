import { fallbackCasinos, fallbackDestinations, fallbackOnlineCasinos } from './fallback-data';
import { hasSupabase, supabase } from './supabase';
import type {
  Casino,
  CasinoDirectoryPage,
  CityDirectoryEntry,
  CountryDirectoryEntry,
  Destination,
  OnlineCasino,
  PublicUrl,
} from './types';

const casinoFields = 'id,legacy_id,name,slug,country_name,country_slug,city_name,city_slug,state_name,short_description,description,editorial_title,editorial_paragraphs,summary,games_description,fun_facts,seo_title,seo_description,seo_keywords,opened_on,gaming_machines,poker_tables,table_games,square_footage,hotel_name,owner_name,official_website_url,street_address,phone,wikidata_id,verified_opened_on,verified_operator_name,enrichment_status,data_confidence,verification_source_urls,verified_fields,last_verified_at,always_open,has_sportsbook,has_bingo,has_slots,has_table_games,longitude,latitude,legacy_image_name,has_original_image,published,updated_at';
export const CITY_PAGE_SIZE = 48;

function dataError(context: string, error: { message?: string } | null | undefined): Error {
  return new Error(`${context}: ${error?.message || 'directory data unavailable'}`);
}

function latestDate(current: string | null | undefined, candidate: string | null | undefined) {
  if (!candidate) return current ?? null;
  if (!current) return candidate;
  return candidate > current ? candidate : current;
}

export async function getSiteStats() {
  if (!supabase) return { casinos: 7493, countries: 157, cities: 3224 };
  const [casinos, destinations] = await Promise.all([
    supabase.from('casinos').select('id', { count: 'exact', head: true }).eq('published', true),
    supabase.rpc('directory_stats'),
  ]);
  if (casinos.error || destinations.error) return { casinos: 7493, countries: 157, cities: 3224 };
  const stats = destinations.data?.[0];
  return {
    casinos: casinos.count ?? 7493,
    countries: Number(stats?.countries ?? 157),
    cities: Number(stats?.cities ?? 3224),
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

export async function getCountriesDirectory(): Promise<CountryDirectoryEntry[]> {
  if (!supabase) {
    const countries = new Map<string, CountryDirectoryEntry & { cities: Set<string> }>();
    for (const casino of fallbackCasinos) {
      const current = countries.get(casino.country_slug) ?? {
        country_name: casino.country_name,
        country_slug: casino.country_slug,
        casino_count: 0,
        city_count: 0,
        last_updated_at: casino.updated_at,
        cities: new Set<string>(),
      };
      current.casino_count += 1;
      current.cities.add(casino.city_slug);
      current.city_count = current.cities.size;
      current.last_updated_at = latestDate(current.last_updated_at, casino.updated_at);
      countries.set(casino.country_slug, current);
    }
    return [...countries.values()]
      .map(({ cities: _cities, ...country }) => country)
      .sort((a, b) => a.country_name.localeCompare(b.country_name, 'en'));
  }

  const { data, error } = await supabase.rpc('directory_countries');
  if (error) throw dataError('Unable to load country directory', error);
  return ((data ?? []) as CountryDirectoryEntry[]).map((country) => ({
    ...country,
    casino_count: Number(country.casino_count),
    city_count: Number(country.city_count),
  }));
}

export async function getCitiesDirectory(country: string): Promise<CityDirectoryEntry[]> {
  if (!supabase) {
    const cities = new Map<string, CityDirectoryEntry>();
    for (const casino of fallbackCasinos.filter((item) => item.country_slug === country)) {
      const current = cities.get(casino.city_slug) ?? {
        country_name: casino.country_name,
        country_slug: casino.country_slug,
        city_name: casino.city_name,
        city_slug: casino.city_slug,
        casino_count: 0,
        last_updated_at: casino.updated_at,
      };
      current.casino_count += 1;
      current.last_updated_at = latestDate(current.last_updated_at, casino.updated_at);
      cities.set(casino.city_slug, current);
    }
    return [...cities.values()].sort((a, b) => a.city_name.localeCompare(b.city_name, 'en'));
  }

  const { data, error } = await supabase.rpc('directory_cities', { selected_country_slug: country });
  if (error) throw dataError(`Unable to load cities for ${country}`, error);
  return ((data ?? []) as CityDirectoryEntry[]).map((city) => ({ ...city, casino_count: Number(city.casino_count) }));
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
    if (error) throw dataError(`Unable to load online casino ${slug}`, error);
    return data ? data as OnlineCasino : null;
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
    if (error) throw dataError(`Unable to load casino ${country}/${city}/${slug}`, error);
    return data ? data as Casino : null;
  }
  return fallbackCasinos.find((casino) => casino.country_slug === country && casino.city_slug === city && casino.slug === slug) ?? null;
}

export async function getCasinosByLocation(country: string, city?: string, limit = 60): Promise<Casino[]> {
  if (supabase) {
    let query = supabase.from('casinos').select(casinoFields).eq('country_slug', country).eq('published', true).order('name').limit(limit);
    if (city) query = query.eq('city_slug', city);
    const { data, error } = await query;
    if (error) throw dataError(`Unable to load casinos for ${country}${city ? `/${city}` : ''}`, error);
    return (data ?? []) as Casino[];
  }
  return fallbackCasinos.filter((casino) => casino.country_slug === country && (!city || casino.city_slug === city));
}

export async function getCasinoDirectoryPage(
  country: string,
  city: string,
  requestedPage = 1,
  requestedPageSize = CITY_PAGE_SIZE,
): Promise<CasinoDirectoryPage> {
  const page = Math.max(1, Math.floor(requestedPage));
  const pageSize = Math.min(60, Math.max(12, Math.floor(requestedPageSize)));
  const from = (page - 1) * pageSize;
  const to = from + pageSize - 1;

  if (!supabase) {
    const matches = fallbackCasinos
      .filter((casino) => casino.country_slug === country && casino.city_slug === city)
      .sort((a, b) => a.name.localeCompare(b.name, 'en'));
    return { casinos: matches.slice(from, to + 1), total: matches.length, page, pageSize };
  }

  const { data, error, count } = await supabase
    .from('casinos')
    .select(casinoFields, { count: 'exact' })
    .eq('country_slug', country)
    .eq('city_slug', city)
    .eq('published', true)
    .order('name')
    .range(from, to);
  if (error) throw dataError(`Unable to load casino page for ${country}/${city}`, error);
  return { casinos: (data ?? []) as Casino[], total: count ?? 0, page, pageSize };
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

export async function getAllPublicUrls(): Promise<PublicUrl[]> {
  if (!supabase || !hasSupabase) {
    const countries = new Map<string, string | null>();
    const cities = new Map<string, { updatedAt: string | null; count: number }>();
    const urls = fallbackCasinos.map((casino) => {
      const countryPath = `/${casino.country_slug}`;
      const cityPath = `/${casino.country_slug}/${casino.city_slug}`;
      countries.set(countryPath, latestDate(countries.get(countryPath), casino.updated_at));
      const city = cities.get(cityPath) ?? { updatedAt: null, count: 0 };
      city.updatedAt = latestDate(city.updatedAt, casino.updated_at);
      city.count += 1;
      cities.set(cityPath, city);
      return { path: `${cityPath}/${casino.slug}`, lastModified: casino.updated_at };
    });
    for (const [path, lastModified] of countries) urls.push({ path, lastModified });
    for (const [path, city] of cities) {
      urls.push({ path, lastModified: city.updatedAt });
      for (let page = 2; page <= Math.ceil(city.count / CITY_PAGE_SIZE); page += 1) {
        urls.push({ path: `${path}/page/${page}`, lastModified: city.updatedAt });
      }
    }
    urls.push(...fallbackOnlineCasinos.filter((casino) => casino.published).map((casino) => ({ path: `/online/${casino.slug}`, lastModified: casino.last_verified_at || casino.reviewed_at })));
    return urls;
  }

  const urls: PublicUrl[] = [];
  const countries = new Map<string, string | null>();
  const cities = new Map<string, { updatedAt: string | null; count: number }>();
  for (let from = 0; ; from += 1000) {
    const { data, error } = await supabase
      .from('casinos')
      .select('id,country_slug,city_slug,slug,updated_at')
      .eq('published', true)
      .order('id')
      .range(from, from + 999);
    if (error) throw dataError('Unable to build casino sitemap', error);
    if (!data?.length) break;
    for (const row of data) {
      const countryPath = `/${row.country_slug}`;
      const cityPath = `${countryPath}/${row.city_slug}`;
      urls.push({ path: `${cityPath}/${row.slug}`, lastModified: row.updated_at });
      countries.set(countryPath, latestDate(countries.get(countryPath), row.updated_at));
      const currentCity = cities.get(cityPath) ?? { updatedAt: null, count: 0 };
      currentCity.updatedAt = latestDate(currentCity.updatedAt, row.updated_at);
      currentCity.count += 1;
      cities.set(cityPath, currentCity);
    }
    if (data.length < 1000) break;
  }

  for (const [path, lastModified] of countries) urls.push({ path, lastModified });
  for (const [path, city] of cities) {
    urls.push({ path, lastModified: city.updatedAt });
    for (let page = 2; page <= Math.ceil(city.count / CITY_PAGE_SIZE); page += 1) {
      urls.push({ path: `${path}/page/${page}`, lastModified: city.updatedAt });
    }
  }

  const { data: online, error: onlineError } = await supabase
    .from('online_casinos')
    .select('slug,updated_at')
    .eq('published', true)
    .order('id');
  if (onlineError) throw dataError('Unable to build online casino sitemap', onlineError);
  urls.push(...(online ?? []).map((row) => ({ path: `/online/${row.slug}`, lastModified: row.updated_at })));
  return urls;
}
