create schema if not exists extensions;
create schema if not exists private;

create extension if not exists postgis with schema extensions;
create extension if not exists pg_trgm with schema extensions;

revoke all on schema private from public, anon;
grant usage on schema private to authenticated;

create or replace function private.is_admin()
returns boolean
language sql
stable
set search_path = ''
as $$
  select coalesce((auth.jwt() -> 'app_metadata' ->> 'role') = 'admin', false)
    and (select auth.uid()) is not null;
$$;

revoke all on function private.is_admin() from public, anon;
grant execute on function private.is_admin() to authenticated;

create table public.casinos (
  id bigint generated always as identity primary key,
  legacy_id bigint unique,
  source_id text,
  name text not null,
  slug text not null,
  country_name text not null,
  country_slug text not null,
  country_iso_code text,
  state_name text,
  city_name text not null,
  city_slug text not null,
  short_description text,
  description text,
  opened_on date,
  gaming_machines integer check (gaming_machines is null or gaming_machines >= 0),
  poker_tables integer check (poker_tables is null or poker_tables >= 0),
  table_games integer check (table_games is null or table_games >= 0),
  square_footage integer check (square_footage is null or square_footage >= 0),
  hotel_name text,
  owner_name text,
  always_open boolean,
  has_sportsbook boolean not null default false,
  has_horse_racing boolean not null default false,
  has_simulcasting boolean not null default false,
  has_offtrack_betting boolean not null default false,
  has_greyhounds boolean not null default false,
  has_bingo boolean not null default false,
  has_slots boolean not null default false,
  has_table_games boolean not null default false,
  longitude double precision check (longitude is null or longitude between -180 and 180),
  latitude double precision check (latitude is null or latitude between -90 and 90),
  location extensions.geography(point, 4326),
  legacy_image_name text,
  featured boolean not null default false,
  published boolean not null default true,
  search_vector tsvector generated always as (
    setweight(to_tsvector('simple', coalesce(name, '')), 'A') ||
    setweight(to_tsvector('simple', coalesce(city_name, '')), 'B') ||
    setweight(to_tsvector('simple', coalesce(country_name, '')), 'B') ||
    setweight(to_tsvector('simple', coalesce(short_description, '')), 'C')
  ) stored,
  created_at timestamptz not null default now(),
  updated_at timestamptz not null default now(),
  constraint casinos_public_path_key unique (country_slug, city_slug, slug),
  constraint casinos_slug_format check (slug ~ '^[A-Za-z0-9_-]+$'),
  constraint casinos_country_slug_format check (country_slug ~ '^[a-z0-9-]+$'),
  constraint casinos_city_slug_format check (city_slug ~ '^[A-Za-z0-9-]+$')
);

create table public.online_casinos (
  id bigint generated always as identity primary key,
  legacy_id bigint unique,
  position smallint check (position is null or position between 1 and 10),
  name text not null,
  slug text not null unique,
  subtitle text,
  rating numeric(2,1) not null default 0 check (rating between 0 and 5),
  bonus text not null default '',
  summary text not null default '',
  description text,
  bonus_description text,
  deposit_description text,
  contact_description text,
  affiliate_url text not null default '',
  logo_url text,
  key_features text[] not null default '{}',
  pros text[] not null default '{}',
  cons text[] not null default '{}',
  deposit_methods text[] not null default '{}',
  contact_methods text[] not null default '{}',
  affiliate_disclosure text,
  active boolean not null default false,
  published boolean not null default false,
  created_at timestamptz not null default now(),
  updated_at timestamptz not null default now(),
  constraint online_casinos_slug_format check (slug ~ '^[A-Za-z0-9_-]+$')
);

create table public.admin_audit_log (
  id bigint generated always as identity primary key,
  actor_id uuid,
  table_name text not null,
  record_id text not null,
  action text not null check (action in ('INSERT', 'UPDATE', 'DELETE')),
  old_data jsonb,
  new_data jsonb,
  created_at timestamptz not null default now()
);

create unique index online_casinos_active_position_key
  on public.online_casinos (position)
  where active and position is not null;
create index online_casinos_public_ranking_idx
  on public.online_casinos (position)
  where active and published;
create index casinos_public_location_idx
  on public.casinos using gist (location)
  where published and location is not null;
create index casinos_public_country_city_idx
  on public.casinos (country_slug, city_slug, name)
  where published;
create index casinos_public_search_idx
  on public.casinos using gin (search_vector)
  where published;
create index admin_audit_log_created_idx on public.admin_audit_log (created_at desc);
create index admin_audit_log_actor_idx on public.admin_audit_log (actor_id, created_at desc);

create or replace function private.set_updated_at()
returns trigger
language plpgsql
set search_path = ''
as $$
begin
  new.updated_at = now();
  return new;
end;
$$;

create or replace function private.sync_casino_location()
returns trigger
language plpgsql
set search_path = ''
as $$
begin
  if new.longitude is not null and new.latitude is not null then
    new.location = extensions.st_setsrid(extensions.st_makepoint(new.longitude, new.latitude), 4326)::extensions.geography;
  else
    new.location = null;
  end if;
  return new;
end;
$$;

create or replace function private.audit_admin_change()
returns trigger
language plpgsql
security definer
set search_path = ''
as $$
declare
  changed_id text;
begin
  changed_id := coalesce(new.id::text, old.id::text);
  insert into public.admin_audit_log (actor_id, table_name, record_id, action, old_data, new_data)
  values ((select auth.uid()), tg_table_name, changed_id, tg_op,
    case when tg_op in ('UPDATE', 'DELETE') then to_jsonb(old) else null end,
    case when tg_op in ('INSERT', 'UPDATE') then to_jsonb(new) else null end);
  if tg_op = 'DELETE' then
    return old;
  end if;
  return new;
end;
$$;

revoke all on function private.set_updated_at() from public, anon, authenticated;
revoke all on function private.sync_casino_location() from public, anon, authenticated;
revoke all on function private.audit_admin_change() from public, anon, authenticated;

create trigger casinos_set_updated_at before update on public.casinos
  for each row execute function private.set_updated_at();
create trigger casinos_sync_location before insert or update of longitude, latitude on public.casinos
  for each row execute function private.sync_casino_location();
create trigger online_casinos_set_updated_at before update on public.online_casinos
  for each row execute function private.set_updated_at();
create trigger online_casinos_audit after insert or update or delete on public.online_casinos
  for each row execute function private.audit_admin_change();

alter table public.casinos enable row level security;
alter table public.online_casinos enable row level security;
alter table public.admin_audit_log enable row level security;

create policy casinos_public_read on public.casinos
  for select to anon, authenticated
  using (published);
create policy casinos_admin_insert on public.casinos
  for insert to authenticated
  with check ((select private.is_admin()));
create policy casinos_admin_update on public.casinos
  for update to authenticated
  using ((select private.is_admin()))
  with check ((select private.is_admin()));
create policy casinos_admin_delete on public.casinos
  for delete to authenticated
  using ((select private.is_admin()));

create policy online_casinos_public_read on public.online_casinos
  for select to anon, authenticated
  using (active and published);
create policy online_casinos_admin_read on public.online_casinos
  for select to authenticated
  using ((select private.is_admin()));
create policy online_casinos_admin_insert on public.online_casinos
  for insert to authenticated
  with check ((select private.is_admin()));
create policy online_casinos_admin_update on public.online_casinos
  for update to authenticated
  using ((select private.is_admin()))
  with check ((select private.is_admin()));
create policy online_casinos_admin_delete on public.online_casinos
  for delete to authenticated
  using ((select private.is_admin()));

create policy admin_audit_log_admin_read on public.admin_audit_log
  for select to authenticated
  using ((select private.is_admin()));

grant usage on schema public to anon, authenticated;
grant select on public.casinos, public.online_casinos to anon, authenticated;
grant insert, update, delete on public.casinos, public.online_casinos to authenticated;
grant select on public.admin_audit_log to authenticated;
grant usage, select on all sequences in schema public to authenticated;

create or replace function public.directory_stats()
returns table (countries bigint, cities bigint)
language sql
stable
security invoker
set search_path = ''
as $$
  select count(distinct country_slug), count(distinct (country_slug, city_slug))
  from public.casinos
  where published;
$$;

create or replace function public.popular_destinations(result_limit integer default 6)
returns table (country_name text, country_slug text, casino_count bigint)
language sql
stable
security invoker
set search_path = ''
as $$
  select min(c.country_name), c.country_slug, count(*)
  from public.casinos c
  where c.published
  group by c.country_slug
  order by count(*) desc, min(c.country_name)
  limit least(greatest(result_limit, 1), 50);
$$;

create or replace function public.search_casinos(search_term text, result_limit integer default 30)
returns table (
  id bigint, legacy_id bigint, name text, slug text, country_name text, country_slug text,
  city_name text, city_slug text, state_name text, short_description text, description text,
  opened_on date, gaming_machines integer, poker_tables integer, table_games integer,
  square_footage integer, hotel_name text, owner_name text, always_open boolean,
  has_sportsbook boolean, has_bingo boolean, has_slots boolean, has_table_games boolean,
  longitude double precision, latitude double precision, published boolean
)
language sql
stable
security invoker
set search_path = ''
as $$
  with query as (select websearch_to_tsquery('simple', search_term) value)
  select c.id, c.legacy_id, c.name, c.slug, c.country_name, c.country_slug,
    c.city_name, c.city_slug, c.state_name, c.short_description, c.description,
    c.opened_on, c.gaming_machines, c.poker_tables, c.table_games,
    c.square_footage, c.hotel_name, c.owner_name, c.always_open,
    c.has_sportsbook, c.has_bingo, c.has_slots, c.has_table_games,
    c.longitude, c.latitude, c.published
  from public.casinos c cross join query q
  where c.published and c.search_vector @@ q.value
  order by ts_rank(c.search_vector, q.value) desc, c.name
  limit least(greatest(result_limit, 1), 100);
$$;

create or replace function public.casinos_in_view(
  west double precision,
  south double precision,
  east double precision,
  north double precision,
  result_limit integer default 250
)
returns table (
  id bigint, name text, slug text, country_name text, country_slug text,
  city_name text, city_slug text, longitude double precision, latitude double precision
)
language sql
stable
security invoker
set search_path = ''
as $$
  select c.id, c.name, c.slug, c.country_name, c.country_slug,
    c.city_name, c.city_slug, c.longitude, c.latitude
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

create or replace function public.save_online_ranking(ranking jsonb)
returns void
language plpgsql
security invoker
set search_path = ''
as $$
declare
  item_count integer;
  updated_count integer;
begin
  if not (select private.is_admin()) then
    raise exception 'Admin role required' using errcode = '42501';
  end if;
  if jsonb_typeof(ranking) <> 'array' then
    raise exception 'Ranking must be a JSON array' using errcode = '22023';
  end if;
  item_count := jsonb_array_length(ranking);
  if item_count > 10 then
    raise exception 'Ranking cannot contain more than 10 operators' using errcode = '23514';
  end if;
  if exists (
    select 1 from jsonb_to_recordset(ranking) as item(id bigint, position smallint)
    group by item.position having item.position is null or item.position not between 1 and 10 or count(*) > 1
  ) then
    raise exception 'Ranking positions must be unique values from 1 to 10' using errcode = '23514';
  end if;

  update public.online_casinos set position = null where position is not null;
  update public.online_casinos c
    set position = item.position
    from jsonb_to_recordset(ranking) as item(id bigint, position smallint)
    where c.id = item.id and c.active;
  get diagnostics updated_count = row_count;
  if updated_count <> item_count then
    raise exception 'Ranking contains unknown or inactive operators' using errcode = '23503';
  end if;
end;
$$;

grant execute on function public.directory_stats() to anon, authenticated;
grant execute on function public.popular_destinations(integer) to anon, authenticated;
grant execute on function public.search_casinos(text, integer) to anon, authenticated;
grant execute on function public.casinos_in_view(double precision, double precision, double precision, double precision, integer) to anon, authenticated;
revoke all on function public.save_online_ranking(jsonb) from public, anon;
grant execute on function public.save_online_ranking(jsonb) to authenticated;

insert into public.online_casinos
  (position, name, slug, subtitle, rating, bonus, summary, description, affiliate_url, key_features, pros, cons, deposit_methods, contact_methods, active, published)
values
  (1, 'DuckyLuck', 'DuckyLuck-Casino', 'A quack above the rest in online gaming', 4.9, '500% Welcome Bonus up to $7,500', 'Over 400 games, modern payment methods and a broad promotional programme.', 'DuckyLuck combines slot games, live dealers and cryptocurrency payments in a mobile-friendly experience.', 'https://get.duckyluck.ag/', array['400+ games','Live dealers','Crypto payments','Mobile compatible'], array['Extensive game library','Generous promotions','Multiple payment options'], array['Limited table games','No sports betting','Young platform'], array['Bitcoin','Mastercard','Visa'], array['Live chat','Email'], true, true),
  (2, 'Wild Casino', 'Wild-Casino', 'A broad catalogue with live dealer games', 4.1, '100% welcome bonus up to $5,000', 'A wide game selection with traditional and cryptocurrency banking options.', 'Wild Casino brings together slots, table games and a live dealer catalogue across desktop and mobile.', 'https://record.commissionkings.ag/', array['400+ games','Live dealer','Crypto banking','24/7 support'], array['Wide game variety','Flexible payment options','Mobile-friendly'], array['No demo mode','Fees on some withdrawals','No sportsbook'], array['Bitcoin','Visa','Mastercard','Bank transfer'], array['Live chat','Email','Phone'], true, true),
  (3, 'LuckyBird', 'LuckyBird-Casino', 'Crypto-first games and competitions', 4.5, 'Free chance to win BTC/ETH/USDT', 'Exclusive games, daily contests and cryptocurrency transactions.', 'LuckyBird focuses on an original game catalogue and a cryptocurrency-led payment experience.', 'https://luckybird.vip/', array['Exclusive games','Crypto payments','Daily contests','VIP programme'], array['Original game selection','Fast crypto payments','High stated payout rate'], array['Limited catalogue','Crypto-only focus','No classic table games'], array['Bitcoin','Litecoin','Ethereum'], array['Live chat','Email'], true, true),
  (4, 'Red Dog', 'Red-Dog-Casino', 'Classic games with cryptocurrency support', 4.8, '225% Welcome Bonus + 20% BTC Deposit', 'Slots, free demo mode and multiple cryptocurrency options.', 'Red Dog offers a compact casino catalogue with a strong focus on slots and crypto payments.', 'https://record.toponepartners.com/', array['150+ games','Demo mode','Crypto support','Mobile compatible'], array['Easy navigation','Demo play','Multiple crypto options'], array['Smaller catalogue','Limited live games','Regional restrictions'], array['Bitcoin','Cards','Cryptocurrency'], array['Live chat','Email'], true, true),
  (5, 'Stake Casino', 'Stake-Casino', 'Social casino experience', 4.6, '$25 No Deposit, 250,000 Gold Coins', 'A social gaming offer with a large catalogue and frequent promotions.', null, 'https://stake.com/', array['Social games','Mobile play','Promotions'], array['Large catalogue','Simple onboarding','Mobile experience'], array['Availability varies','Terms vary by region'], array['Operator-specific methods'], array['Help centre'], true, true),
  (6, 'High 5 Casino', 'High-5-Casino', 'Social casino with daily rewards', 4.4, '600 Diamonds and 250 Game Coins', 'A social casino catalogue built around virtual currencies and recurring rewards.', null, 'https://record.high5affiliates.com/', array['Social casino','Daily rewards','Mobile play'], array['Accessible experience','Frequent rewards','Large slot selection'], array['Virtual currency model','Regional availability'], array['Operator-specific methods'], array['Help centre'], true, true)
on conflict (slug) do nothing;
