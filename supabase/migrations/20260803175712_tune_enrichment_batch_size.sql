-- The production pilot completes a 250-record reconciliation batch well within
-- the function and pg_net timeouts, reducing a full monthly refresh to 4 runs.
update public.casino_enrichment_jobs
set batch_size = 250
where source_name = 'wikidata';
