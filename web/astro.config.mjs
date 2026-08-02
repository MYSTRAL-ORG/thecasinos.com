import { defineConfig } from 'astro/config';
import netlify from '@astrojs/netlify';

const isDevCommand = process.argv.includes('dev');

export default defineConfig({
  site: 'https://www.thecasinos.com',
  output: 'server',
  adapter: isDevCommand ? undefined : netlify(),
  trailingSlash: 'never',
  compressHTML: true,
  security: {
    checkOrigin: true,
  },
  vite: {
    build: {
      sourcemap: false,
    },
  },
});
