revoke all on table public.casinos, public.online_casinos, public.admin_audit_log from public;
revoke insert, update, delete, truncate, references, trigger
  on table public.casinos, public.online_casinos from anon;
revoke all on table public.admin_audit_log from anon;
revoke all on all sequences in schema public from public, anon;

revoke all on function public.directory_stats() from public;
revoke all on function public.popular_destinations(integer) from public;
revoke all on function public.search_casinos(text, integer) from public;
revoke all on function public.casinos_in_view(double precision, double precision, double precision, double precision, integer)
  from public;
revoke all on function public.save_online_ranking(jsonb) from public, anon;
