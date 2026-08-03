/// <reference types="astro/client" />

type NetlifyLocals = import('@astrojs/netlify').NetlifyLocals;

declare namespace App {
  interface Locals {
    netlify?: NetlifyLocals['netlify'];
  }
}

interface ImportMetaEnv {
  readonly PUBLIC_SUPABASE_URL?: string;
  readonly PUBLIC_SUPABASE_ANON_KEY?: string;
}

interface ImportMeta {
  readonly env: ImportMetaEnv;
}
