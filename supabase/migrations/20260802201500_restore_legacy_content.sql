alter table public.casinos
  add column if not exists editorial_title text,
  add column if not exists editorial_paragraphs text[] not null default '{}',
  add column if not exists summary text,
  add column if not exists games_description text,
  add column if not exists fun_facts text,
  add column if not exists seo_title text,
  add column if not exists seo_description text,
  add column if not exists seo_keywords text,
  add column if not exists has_original_image boolean not null default false,
  add column if not exists legacy_content_source_url text,
  add column if not exists legacy_content_imported_at timestamptz;

update public.casinos
set has_original_image = legacy_image_name is not null
  and legacy_image_name not like 'casino-no-pic-%';

update public.casinos
set featured = legacy_id in (664, 1, 1595, 1133, 1132, 3);

drop function if exists public.search_casinos(text, integer);
create function public.search_casinos(search_term text, result_limit integer default 30)
returns table (
  id bigint, legacy_id bigint, name text, slug text, country_name text, country_slug text,
  city_name text, city_slug text, state_name text, short_description text, description text,
  editorial_title text, editorial_paragraphs text[], summary text, games_description text,
  fun_facts text, seo_title text, seo_description text, seo_keywords text,
  opened_on date, gaming_machines integer, poker_tables integer, table_games integer,
  square_footage integer, hotel_name text, owner_name text, always_open boolean,
  has_sportsbook boolean, has_bingo boolean, has_slots boolean, has_table_games boolean,
  longitude double precision, latitude double precision, legacy_image_name text,
  has_original_image boolean, published boolean
)
language sql
stable
security invoker
set search_path = ''
as $$
  with query as (select websearch_to_tsquery('simple', search_term) value)
  select c.id, c.legacy_id, c.name, c.slug, c.country_name, c.country_slug,
    c.city_name, c.city_slug, c.state_name, c.short_description, c.description,
    c.editorial_title, c.editorial_paragraphs, c.summary, c.games_description,
    c.fun_facts, c.seo_title, c.seo_description, c.seo_keywords,
    c.opened_on, c.gaming_machines, c.poker_tables, c.table_games,
    c.square_footage, c.hotel_name, c.owner_name, c.always_open,
    c.has_sportsbook, c.has_bingo, c.has_slots, c.has_table_games,
    c.longitude, c.latitude, c.legacy_image_name, c.has_original_image, c.published
  from public.casinos c cross join query q
  where c.published and (
    c.search_vector @@ q.value
    or to_tsvector('simple', concat_ws(' ', c.editorial_title, c.summary, c.seo_keywords)) @@ q.value
  )
  order by
    ts_rank(c.search_vector, q.value) desc,
    ts_rank(to_tsvector('simple', concat_ws(' ', c.editorial_title, c.summary, c.seo_keywords)), q.value) desc,
    c.name
  limit least(greatest(result_limit, 1), 100);
$$;

drop function if exists public.casinos_in_view(double precision, double precision, double precision, double precision, integer);
create function public.casinos_in_view(
  west double precision,
  south double precision,
  east double precision,
  north double precision,
  result_limit integer default 250
)
returns table (
  id bigint, name text, slug text, country_name text, country_slug text,
  city_name text, city_slug text, longitude double precision, latitude double precision,
  legacy_image_name text, has_original_image boolean
)
language sql
stable
security invoker
set search_path = ''
as $$
  select c.id, c.name, c.slug, c.country_name, c.country_slug,
    c.city_name, c.city_slug, c.longitude, c.latitude,
    c.legacy_image_name, c.has_original_image
  from public.casinos c
  where c.published
    and c.location is not null
    and c.latitude between greatest(south, -90) and least(north, 90)
    and (
      (west <= east and c.location operator(extensions.&&) extensions.st_makeenvelope(greatest(west, -180), greatest(south, -90), least(east, 180), least(north, 90), 4326)::extensions.geography)
      or
      (west > east and (
        c.location operator(extensions.&&) extensions.st_makeenvelope(greatest(west, -180), greatest(south, -90), 180, least(north, 90), 4326)::extensions.geography
        or c.location operator(extensions.&&) extensions.st_makeenvelope(-180, greatest(south, -90), least(east, 180), least(north, 90), 4326)::extensions.geography
      ))
    )
  order by c.featured desc, c.name
  limit least(greatest(result_limit, 1), 500);
$$;

grant execute on function public.search_casinos(text, integer) to anon, authenticated;
grant execute on function public.casinos_in_view(double precision, double precision, double precision, double precision, integer) to anon, authenticated;
