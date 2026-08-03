import type { Casino } from './types';

export function getCasinoImageSource(casino: Pick<Casino, 'legacy_image_name' | 'has_original_image'>): string | null {
  const imageName = casino.legacy_image_name?.trim();
  if (!imageName) return null;

  const placeholderMatch = imageName.match(/^casino-no-pic-([1-8])\.webp$/i);
  const originalIsAvailable = casino.has_original_image !== false && imageName.toLowerCase().endsWith('.webp');
  const resolvedName = originalIsAvailable
    ? imageName
    : placeholderMatch?.[0].toLowerCase() ?? getFallbackImageName(imageName);
  const directory = originalIsAvailable ? '/media/casinos/' : '/media/casino-placeholders/';
  return `${directory}${encodeURIComponent(resolvedName)}`;
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

function getFallbackImageName(seed: string): string {
  let hash = 0;
  for (const character of seed) hash = ((hash * 31) + character.charCodeAt(0)) >>> 0;
  return `casino-no-pic-${(hash % 8) + 1}.webp`;
}
