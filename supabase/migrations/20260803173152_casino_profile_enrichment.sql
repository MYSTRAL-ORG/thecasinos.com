create extension if not exists pgcrypto with schema extensions;
create extension if not exists unaccent with schema extensions;
create extension if not exists pg_net;
create extension if not exists pg_cron with schema pg_catalog;

alter table public.casinos
  add column if not exists official_website_url text,
  add column if not exists street_address text,
  add column if not exists phone text,
  add column if not exists wikidata_id text,
  add column if not exists verified_opened_on date,
  add column if not exists verified_operator_name text,
  add column if not exists enrichment_status text not null default 'queued',
  add column if not exists data_confidence numeric(4,3),
  add column if not exists verification_source_urls text[] not null default '{}',
  add column if not exists verified_fields jsonb not null default '{}',
  add column if not exists last_verified_at timestamptz;

do $$
begin
  if not exists (
    select 1 from pg_constraint
    where conname = 'casinos_wikidata_id_format'
      and conrelid = 'public.casinos'::regclass
  ) then
    alter table public.casinos
      add constraint casinos_wikidata_id_format
      check (wikidata_id is null or wikidata_id ~ '^Q[1-9][0-9]*$');
  end if;

  if not exists (
    select 1 from pg_constraint
    where conname = 'casinos_enrichment_status_check'
      and conrelid = 'public.casinos'::regclass
  ) then
    alter table public.casinos
      add constraint casinos_enrichment_status_check
      check (enrichment_status in ('queued', 'candidate', 'verified_open_data', 'verified_official', 'unmatched', 'manual_review'));
  end if;

  if not exists (
    select 1 from pg_constraint
    where conname = 'casinos_data_confidence_check'
      and conrelid = 'public.casinos'::regclass
  ) then
    alter table public.casinos
      add constraint casinos_data_confidence_check
      check (data_confidence is null or data_confidence between 0 and 1);
  end if;
end $$;

create table if not exists public.casino_enrichment_jobs (
  id bigint generated always as identity primary key,
  source_name text not null unique,
  status text not null default 'queued'
    check (status in ('queued', 'running', 'complete', 'error', 'paused')),
  cursor integer not null default 0 check (cursor >= 0),
  batch_size smallint not null default 100 check (batch_size between 1 and 250),
  expected_records integer not null default 941 check (expected_records > 0),
  records_fetched integer not null default 0 check (records_fetched >= 0),
  records_matched integer not null default 0 check (records_matched >= 0),
  records_auto_applied integer not null default 0 check (records_auto_applied >= 0),
  last_error text,
  locked_until timestamptz,
  last_run_at timestamptz,
  next_refresh_at timestamptz,
  created_at timestamptz not null default now(),
  updated_at timestamptz not null default now()
);

create table if not exists public.casino_enrichment_source_records (
  id bigint generated always as identity primary key,
  source_name text not null,
  external_id text not null,
  external_url text not null,
  name text not null,
  country_code text,
  longitude double precision not null check (longitude between -180 and 180),
  latitude double precision not null check (latitude between -90 and 90),
  location extensions.geography(point, 4326),
  website_url text,
  opened_on date,
  operator_name text,
  street_address text,
  phone text,
  raw_payload jsonb not null default '{}',
  matched_casino_id bigint references public.casinos(id) on delete set null,
  match_score numeric(4,3) check (match_score is null or match_score between 0 and 1),
  name_similarity numeric(4,3) check (name_similarity is null or name_similarity between 0 and 1),
  distance_m numeric(12,2) check (distance_m is null or distance_m >= 0),
  match_status text not null default 'pending'
    check (match_status in ('pending', 'unmatched', 'candidate', 'auto_applied', 'rejected')),
  match_reason text,
  auto_applied_fields text[] not null default '{}',
  fetched_at timestamptz not null default now(),
  created_at timestamptz not null default now(),
  updated_at timestamptz not null default now(),
  unique (source_name, external_id)
);

create table if not exists private.casino_enrichment_settings (
  singleton boolean primary key default true check (singleton),
  cron_token_hash text not null,
  created_at timestamptz not null default now(),
  updated_at timestamptz not null default now()
);

create index if not exists casino_enrichment_records_location_idx
  on public.casino_enrichment_source_records using gist (location);
create index if not exists casino_enrichment_records_casino_idx
  on public.casino_enrichment_source_records (matched_casino_id)
  where matched_casino_id is not null;
create index if not exists casino_enrichment_records_status_idx
  on public.casino_enrichment_source_records (source_name, match_status, fetched_at desc);
create index if not exists casinos_wikidata_id_idx
  on public.casinos (wikidata_id)
  where wikidata_id is not null;
create index if not exists casinos_enrichment_queue_idx
  on public.casinos (enrichment_status, id)
  where published and enrichment_status <> 'verified_official';

create or replace function private.normalize_casino_name(value text)
returns text
language sql
stable
strict
set search_path = ''
as $$
  select trim(regexp_replace(
    regexp_replace(
      extensions.unaccent(lower(value)),
      '\m(the|casino|hotel|resort|gaming|and|at|de|du|des|la|le|les|el|del)\M',
      ' ',
      'g'
    ),
    '[^a-z0-9]+',
    ' ',
    'g'
  ));
$$;

create or replace function private.sync_enrichment_source_location()
returns trigger
language plpgsql
set search_path = ''
as $$
begin
  new.location = extensions.st_setsrid(
    extensions.st_makepoint(new.longitude, new.latitude),
    4326
  )::extensions.geography;
  return new;
end;
$$;

create or replace function private.reconcile_enrichment_source_record()
returns trigger
language plpgsql
set search_path = ''
as $$
declare
  best_casino_id bigint;
  best_name_similarity double precision;
  best_distance_m double precision;
  best_match_score double precision;
  runner_up_score double precision;
  selected_status text := 'unmatched';
  selected_reason text := 'No compatible casino found within 10 km.';
  applied_fields text[] := '{}';
  source_fields jsonb := '{}';
begin
  if new.source_name <> 'wikidata' or new.location is null then
    return new;
  end if;

  with nearby as (
    select
      c.id as casino_id,
      greatest(
        extensions.similarity(private.normalize_casino_name(c.name), private.normalize_casino_name(new.name)),
        extensions.word_similarity(private.normalize_casino_name(c.name), private.normalize_casino_name(new.name)),
        extensions.word_similarity(private.normalize_casino_name(new.name), private.normalize_casino_name(c.name))
      ) as candidate_name_similarity,
      extensions.st_distance(c.location, new.location) as candidate_distance_m
    from public.casinos c
    where c.published
      and c.location is not null
      and extensions.st_dwithin(c.location, new.location, 10000)
      and (
        new.country_code is null
        or c.country_iso_code is null
        or upper(c.country_iso_code) = upper(new.country_code)
      )
  ), scored as (
    select
      casino_id,
      candidate_name_similarity,
      candidate_distance_m,
      least(1.0, greatest(0.0,
        candidate_name_similarity * 0.70
        + greatest(0.0, 1.0 - candidate_distance_m / 5000.0) * 0.30
      )) as candidate_match_score
    from nearby
  ), ranked as (
    select
      *,
      lead(candidate_match_score) over (order by candidate_match_score desc, candidate_distance_m, casino_id) as second_score
    from scored
  )
  select casino_id, candidate_name_similarity, candidate_distance_m, candidate_match_score, second_score
  into best_casino_id, best_name_similarity, best_distance_m, best_match_score, runner_up_score
  from ranked
  order by candidate_match_score desc, candidate_distance_m, casino_id
  limit 1;

  if best_casino_id is not null then
    if best_match_score >= 0.82
      and best_name_similarity >= 0.70
      and best_distance_m <= 1500
      and (runner_up_score is null or best_match_score - runner_up_score >= 0.08)
      and not exists (
        select 1
        from public.casino_enrichment_source_records existing
        where existing.source_name = new.source_name
          and existing.matched_casino_id = best_casino_id
          and existing.match_status = 'auto_applied'
          and existing.id <> new.id
      )
    then
      selected_status := 'auto_applied';
      selected_reason := 'Unique high-confidence name and location match.';
    elsif best_match_score >= 0.68
      and best_name_similarity >= 0.55
      and best_distance_m <= 5000
    then
      selected_status := 'candidate';
      selected_reason := case
        when runner_up_score is not null and best_match_score - runner_up_score < 0.08
          then 'Candidate retained because two nearby listings are too similar.'
        else 'Candidate retained below the automatic-apply threshold.'
      end;
    else
      selected_reason := 'Nearby result did not meet the minimum name and distance thresholds.';
    end if;
  end if;

  if selected_status = 'auto_applied' then
    applied_fields := array_remove(array[
      'wikidata_id',
      case when new.website_url is not null then 'official_website_url' end,
      case when new.opened_on is not null then 'verified_opened_on' end,
      case when new.operator_name is not null then 'verified_operator_name' end,
      case when new.street_address is not null then 'street_address' end,
      case when new.phone is not null then 'phone' end
    ], null);

    update public.casinos c
    set
      wikidata_id = case
        when c.wikidata_id is null or c.wikidata_id = new.external_id then new.external_id
        else c.wikidata_id
      end,
      official_website_url = case
        when new.website_url is not null
          and (c.official_website_url is null or c.verified_fields #>> '{official_website_url,source}' = 'wikidata')
          then new.website_url
        else c.official_website_url
      end,
      verified_opened_on = case
        when new.opened_on is not null
          and (c.verified_opened_on is null or c.verified_fields #>> '{verified_opened_on,source}' = 'wikidata')
          then new.opened_on
        else c.verified_opened_on
      end,
      verified_operator_name = case
        when new.operator_name is not null
          and (c.verified_operator_name is null or c.verified_fields #>> '{verified_operator_name,source}' = 'wikidata')
          then new.operator_name
        else c.verified_operator_name
      end,
      street_address = case
        when new.street_address is not null
          and (c.street_address is null or c.verified_fields #>> '{street_address,source}' = 'wikidata')
          then new.street_address
        else c.street_address
      end,
      phone = case
        when new.phone is not null
          and (c.phone is null or c.verified_fields #>> '{phone,source}' = 'wikidata')
          then new.phone
        else c.phone
      end,
      enrichment_status = 'verified_open_data',
      data_confidence = greatest(coalesce(c.data_confidence, 0), best_match_score),
      verification_source_urls = array(
        select distinct source_url
        from unnest(coalesce(c.verification_source_urls, '{}') || array[new.external_url]) source_url
        where source_url is not null and source_url <> ''
      ),
      verified_fields = coalesce(c.verified_fields, '{}') || jsonb_strip_nulls(jsonb_build_object(
        'wikidata_id', jsonb_build_object('value', new.external_id, 'source', 'wikidata', 'source_url', new.external_url, 'verified_at', new.fetched_at),
        'official_website_url', case
          when new.website_url is not null
            and (c.verified_fields #>> '{official_website_url,source}' is null or c.verified_fields #>> '{official_website_url,source}' = 'wikidata')
            then jsonb_build_object('value', new.website_url, 'source', 'wikidata', 'source_url', new.external_url, 'verified_at', new.fetched_at)
        end,
        'verified_opened_on', case
          when new.opened_on is not null
            and (c.verified_fields #>> '{verified_opened_on,source}' is null or c.verified_fields #>> '{verified_opened_on,source}' = 'wikidata')
            then jsonb_build_object('value', new.opened_on, 'source', 'wikidata', 'source_url', new.external_url, 'verified_at', new.fetched_at)
        end,
        'verified_operator_name', case
          when new.operator_name is not null
            and (c.verified_fields #>> '{verified_operator_name,source}' is null or c.verified_fields #>> '{verified_operator_name,source}' = 'wikidata')
            then jsonb_build_object('value', new.operator_name, 'source', 'wikidata', 'source_url', new.external_url, 'verified_at', new.fetched_at)
        end,
        'street_address', case
          when new.street_address is not null
            and (c.verified_fields #>> '{street_address,source}' is null or c.verified_fields #>> '{street_address,source}' = 'wikidata')
            then jsonb_build_object('value', new.street_address, 'source', 'wikidata', 'source_url', new.external_url, 'verified_at', new.fetched_at)
        end,
        'phone', case
          when new.phone is not null
            and (c.verified_fields #>> '{phone,source}' is null or c.verified_fields #>> '{phone,source}' = 'wikidata')
            then jsonb_build_object('value', new.phone, 'source', 'wikidata', 'source_url', new.external_url, 'verified_at', new.fetched_at)
        end
      )),
      last_verified_at = greatest(coalesce(c.last_verified_at, '-infinity'::timestamptz), new.fetched_at)
    where c.id = best_casino_id;
  elsif selected_status = 'candidate' then
    update public.casinos c
    set
      enrichment_status = case when c.enrichment_status = 'queued' then 'candidate' else c.enrichment_status end,
      data_confidence = greatest(coalesce(c.data_confidence, 0), best_match_score)
    where c.id = best_casino_id;
  end if;

  source_fields := jsonb_strip_nulls(jsonb_build_object(
    'website_url', new.website_url,
    'opened_on', new.opened_on,
    'operator_name', new.operator_name,
    'street_address', new.street_address,
    'phone', new.phone
  ));

  update public.casino_enrichment_source_records record
  set
    matched_casino_id = best_casino_id,
    match_score = best_match_score,
    name_similarity = best_name_similarity,
    distance_m = best_distance_m,
    match_status = selected_status,
    match_reason = selected_reason || case when source_fields = '{}' then ' No additional fact fields were present.' else '' end,
    auto_applied_fields = case when selected_status = 'auto_applied' then applied_fields else '{}' end,
    updated_at = now()
  where record.id = new.id;

  return new;
end;
$$;

create trigger casino_enrichment_jobs_set_updated_at
  before update on public.casino_enrichment_jobs
  for each row execute function private.set_updated_at();
create trigger casino_enrichment_records_set_updated_at
  before update on public.casino_enrichment_source_records
  for each row execute function private.set_updated_at();
create trigger casino_enrichment_records_sync_location
  before insert or update of longitude, latitude on public.casino_enrichment_source_records
  for each row execute function private.sync_enrichment_source_location();
create trigger casino_enrichment_records_reconcile
  after insert or update of name, country_code, longitude, latitude, website_url, opened_on, operator_name, street_address, phone
  on public.casino_enrichment_source_records
  for each row execute function private.reconcile_enrichment_source_record();

alter table public.casino_enrichment_jobs enable row level security;
alter table public.casino_enrichment_source_records enable row level security;

create policy casino_enrichment_jobs_admin_read on public.casino_enrichment_jobs
  for select to authenticated
  using ((select private.is_admin()));
create policy casino_enrichment_records_admin_read on public.casino_enrichment_source_records
  for select to authenticated
  using ((select private.is_admin()));

grant select on public.casino_enrichment_jobs, public.casino_enrichment_source_records to authenticated;
grant select, insert, update on public.casino_enrichment_jobs, public.casino_enrichment_source_records to service_role;
grant usage, select on sequence public.casino_enrichment_jobs_id_seq to service_role;
grant usage, select on sequence public.casino_enrichment_source_records_id_seq to service_role;

revoke all on private.casino_enrichment_settings from public, anon, authenticated, service_role;
revoke all on function private.normalize_casino_name(text) from public, anon, authenticated, service_role;
revoke all on function private.sync_enrichment_source_location() from public, anon, authenticated, service_role;
revoke all on function private.reconcile_enrichment_source_record() from public, anon, authenticated, service_role;

create or replace function public.validate_enrichment_cron_token(candidate text)
returns boolean
language sql
stable
security definer
set search_path = ''
as $$
  select candidate is not null
    and length(candidate) >= 32
    and exists (
      select 1
      from private.casino_enrichment_settings settings
      where settings.singleton
        and settings.cron_token_hash = encode(extensions.digest(candidate, 'sha256'), 'hex')
    );
$$;

create or replace function public.claim_wikidata_enrichment_batch()
returns table (batch_cursor integer, requested_batch_size smallint)
language plpgsql
security definer
set search_path = ''
as $$
begin
  return query
  update public.casino_enrichment_jobs job
  set
    cursor = case
      when job.status = 'complete' and job.next_refresh_at <= now() then 0
      else job.cursor
    end,
    status = 'running',
    locked_until = now() + interval '8 minutes',
    last_run_at = now(),
    last_error = null
  where job.source_name = 'wikidata'
    and (job.locked_until is null or job.locked_until < now())
    and (
      job.status in ('queued', 'running', 'error')
      or (job.status = 'complete' and job.next_refresh_at <= now())
    )
  returning job.cursor, job.batch_size;
end;
$$;

create or replace function public.finish_wikidata_enrichment_batch(
  processed_cursor integer,
  fetched_count integer,
  has_more boolean,
  error_message text default null
)
returns void
language plpgsql
security definer
set search_path = ''
as $$
begin
  if error_message is not null then
    update public.casino_enrichment_jobs
    set status = 'error', last_error = left(error_message, 2000), locked_until = null
    where source_name = 'wikidata' and cursor = processed_cursor;
    return;
  end if;

  update public.casino_enrichment_jobs job
  set
    cursor = processed_cursor + greatest(fetched_count, 0),
    status = case when has_more then 'queued' else 'complete' end,
    records_fetched = stats.fetched,
    records_matched = stats.matched,
    records_auto_applied = stats.auto_applied,
    expected_records = case when has_more then job.expected_records else greatest(stats.fetched, 1) end,
    last_error = null,
    locked_until = null,
    next_refresh_at = case when has_more then null else now() + interval '30 days' end
  from (
    select
      count(*)::integer as fetched,
      count(*) filter (where match_status in ('candidate', 'auto_applied'))::integer as matched,
      count(*) filter (where match_status = 'auto_applied')::integer as auto_applied
    from public.casino_enrichment_source_records
    where source_name = 'wikidata'
  ) stats
  where job.source_name = 'wikidata' and job.cursor = processed_cursor;
end;
$$;

revoke all on function public.validate_enrichment_cron_token(text) from public, anon, authenticated;
revoke all on function public.claim_wikidata_enrichment_batch() from public, anon, authenticated;
revoke all on function public.finish_wikidata_enrichment_batch(integer, integer, boolean, text) from public, anon, authenticated;
grant execute on function public.validate_enrichment_cron_token(text) to service_role;
grant execute on function public.claim_wikidata_enrichment_batch() to service_role;
grant execute on function public.finish_wikidata_enrichment_batch(integer, integer, boolean, text) to service_role;

create or replace function private.schedule_casino_enrichment()
returns bigint
language plpgsql
set search_path = ''
as $$
declare
  scheduled_job_id bigint;
begin
  if not exists (select 1 from vault.decrypted_secrets where name = 'thecasinos_project_url')
    or not exists (select 1 from vault.decrypted_secrets where name = 'thecasinos_enrichment_cron_token')
  then
    raise exception 'Casino enrichment Vault secrets are not configured.' using errcode = '55000';
  end if;

  select cron.schedule(
    'thecasinos-wikidata-enrichment',
    '*/5 * * * *',
    $cron$
      select net.http_post(
        url := (select decrypted_secret from vault.decrypted_secrets where name = 'thecasinos_project_url') || '/functions/v1/enrich-casinos',
        headers := jsonb_build_object(
          'Content-Type', 'application/json',
          'x-enrichment-token', (select decrypted_secret from vault.decrypted_secrets where name = 'thecasinos_enrichment_cron_token')
        ),
        body := jsonb_build_object('source', 'wikidata', 'scheduled_at', now()),
        timeout_milliseconds := 120000
      );
    $cron$
  ) into scheduled_job_id;

  return scheduled_job_id;
end;
$$;

revoke all on function private.schedule_casino_enrichment() from public, anon, authenticated, service_role;

insert into public.casino_enrichment_jobs (source_name, status, batch_size)
values ('wikidata', 'queued', 100)
on conflict (source_name) do nothing;

comment on table public.casino_enrichment_source_records is
  'Raw external records, match evidence and proposed casino profile enrichments. Not publicly readable.';
comment on column public.casinos.verified_fields is
  'Per-field source, value and verification timestamp. Official/manual sources take precedence over open data.';
comment on column public.casinos.data_confidence is
  'Automated match confidence from 0 to 1; not an editorial rating.';
