import type { Casino } from './types';

const legacyMediaOrigin = (import.meta.env.PUBLIC_LEGACY_MEDIA_ORIGIN || 'https://www.thecasinos.com').replace(/\/$/, '');

export function getCasinoImageSource(casino: Pick<Casino, 'legacy_image_name' | 'has_original_image'>): string | null {
  const imageName = casino.legacy_image_name?.trim();
  if (!imageName) return null;
  const placeholder = casino.has_original_image === false || /^casino-no-pic-[1-8]\.webp$/i.test(imageName);
  const directory = placeholder ? '/img/casinos/randomCasinos/' : '/img/casino/';
  return `${legacyMediaOrigin}${directory}${encodeURIComponent(imageName)}`;
}

export function getCasinoImageUrl(
  casino: Pick<Casino, 'legacy_image_name' | 'has_original_image'>,
  options: { width: number; height?: number; quality?: number; fit?: 'cover' | 'contain' } = { width: 720 },
): string | null {
  const source = getCasinoImageSource(casino);
  if (!source) return null;
  if (import.meta.env.DEV) return source;
  const parameters = new URLSearchParams({
    url: source,
    w: String(options.width),
    fit: options.fit ?? 'cover',
    q: String(options.quality ?? 78),
  });
  if (options.height) parameters.set('h', String(options.height));
  return `/.netlify/images?${parameters}`;
}
