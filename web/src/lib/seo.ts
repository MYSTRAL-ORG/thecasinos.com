import type { Casino } from './types';

export const SITE_NAME = 'TheCasinos.com';
export const SITE_ORIGIN = 'https://www.thecasinos.com';

function normalize(value: string) {
  return value.replace(/\s+/g, ' ').trim();
}

export function truncateMeta(value: string, limit = 160) {
  const normalized = normalize(value);
  if (normalized.length <= limit) return normalized;
  const shortened = normalized.slice(0, limit + 1);
  const lastSpace = shortened.lastIndexOf(' ');
  return `${shortened.slice(0, lastSpace > limit * 0.7 ? lastSpace : limit).replace(/[\s,;:.-]+$/, '')}…`;
}

export function casinoPageTitle(casino: Casino) {
  const legacyTitle = normalize(casino.seo_title || '');
  if (legacyTitle.length >= 24 && legacyTitle.length <= 68) return legacyTitle;

  const name = normalize(casino.name);
  const contextual = `${name} casino in ${casino.city_name}, ${casino.country_name}`;
  if (contextual.length <= 70) return contextual;

  const local = `${name} – ${casino.city_name}`;
  return local.length <= 70 ? local : name;
}

export function casinoPageDescription(casino: Casino) {
  const candidate = normalize(casino.seo_description || '');
  if (candidate.length >= 70 && candidate.length <= 170) return candidate;

  const source = normalize(casino.summary || casino.description || '');
  const fallback = `Plan a visit to ${normalize(casino.name)} in ${casino.city_name}, ${casino.country_name}. Find games, venue facts, photos and location information.`;
  return truncateMeta(source.length >= 70 ? source : `${fallback} ${source}`, 160);
}

export function breadcrumbSchema(items: { name: string; path: string }[], origin = SITE_ORIGIN) {
  return {
    '@context': 'https://schema.org',
    '@type': 'BreadcrumbList',
    itemListElement: items.map((item, index) => ({
      '@type': 'ListItem',
      position: index + 1,
      name: item.name,
      item: new URL(item.path, origin).toString(),
    })),
  };
}
