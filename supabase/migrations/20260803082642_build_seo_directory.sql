create or replace function public.directory_countries()
returns table (
  country_name text,
  country_slug text,
  casino_count bigint,
  city_count bigint,
  last_updated_at timestamptz
)
language sql
stable
security invoker
set search_path = ''
as $$
  select
    min(c.country_name) as country_name,
    c.country_slug,
    count(*) as casino_count,
    count(distinct c.city_slug) as city_count,
    max(c.updated_at) as last_updated_at
  from public.casinos c
  where c.published
  group by c.country_slug
  order by min(c.country_name);
$$;

create or replace function public.directory_cities(selected_country_slug text)
returns table (
  country_name text,
  country_slug text,
  city_name text,
  city_slug text,
  casino_count bigint,
  last_updated_at timestamptz
)
language sql
stable
security invoker
set search_path = ''
as $$
  select
    min(c.country_name) as country_name,
    c.country_slug,
    min(c.city_name) as city_name,
    c.city_slug,
    count(*) as casino_count,
    max(c.updated_at) as last_updated_at
  from public.casinos c
  where c.published
    and c.country_slug = selected_country_slug
  group by c.country_slug, c.city_slug
  order by min(c.city_name);
$$;

revoke all on function public.directory_countries() from public;
revoke all on function public.directory_cities(text) from public;
grant execute on function public.directory_countries() to anon, authenticated;
grant execute on function public.directory_cities(text) to anon, authenticated;
