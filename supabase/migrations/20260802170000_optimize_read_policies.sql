drop policy casinos_public_read on public.casinos;

create policy casinos_public_read on public.casinos
  for select to anon
  using (published);

create policy casinos_authenticated_read on public.casinos
  for select to authenticated
  using (published or (select private.is_admin()));

drop policy online_casinos_public_read on public.online_casinos;
drop policy online_casinos_admin_read on public.online_casinos;

create policy online_casinos_public_read on public.online_casinos
  for select to anon
  using (active and published);

create policy online_casinos_authenticated_read on public.online_casinos
  for select to authenticated
  using ((active and published) or (select private.is_admin()));
