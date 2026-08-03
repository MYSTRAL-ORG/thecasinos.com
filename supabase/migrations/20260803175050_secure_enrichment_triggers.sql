-- Reconciliation runs from service-role writes but needs private normalization
-- helpers. Keep the private schema inaccessible and execute only the trigger
-- bodies with their database-owner privileges.
alter function private.sync_enrichment_source_location() security definer;
alter function private.sync_enrichment_source_location() set search_path = '';

alter function private.reconcile_enrichment_source_record() security definer;
alter function private.reconcile_enrichment_source_record() set search_path = '';

revoke all on function private.sync_enrichment_source_location() from public, anon, authenticated, service_role;
revoke all on function private.reconcile_enrichment_source_record() from public, anon, authenticated, service_role;
