import "jsr:@supabase/functions-js/edge-runtime.d.ts";
import { createClient } from "@supabase/supabase-js";

type SparqlValue = {
  type: string;
  value: string;
  datatype?: string;
  "xml:lang"?: string;
};
type SparqlBinding = Record<string, SparqlValue | undefined>;
type ClaimedBatch = { batch_cursor: number; requested_batch_size: number };

const WIKIDATA_ENDPOINT = "https://query.wikidata.org/sparql";
const USER_AGENT = "TheCasinosBot/1.0 (https://www.thecasinos.com/about)";

function jsonResponse(body: unknown, status = 200) {
  return new Response(JSON.stringify(body), {
    status,
    headers: {
      "Content-Type": "application/json",
      "Cache-Control": "no-store",
    },
  });
}

function getSecretKey() {
  const namedKeys = Deno.env.get("SUPABASE_SECRET_KEYS");
  if (namedKeys) {
    const parsed = JSON.parse(namedKeys) as Record<string, string>;
    if (parsed.default) return parsed.default;
  }
  return Deno.env.get("SUPABASE_SERVICE_ROLE_KEY") ?? "";
}

function sparqlQuery(limit: number, offset: number) {
  return `
SELECT
  ?item
  (SAMPLE(?itemLabelValue) AS ?itemLabel)
  (SAMPLE(?coordValue) AS ?coord)
  (SAMPLE(?websiteValue) AS ?website)
  (MIN(?inceptionValue) AS ?inception)
  (SAMPLE(?operatorLabelValue) AS ?operatorLabel)
  (SAMPLE(?streetAddressValue) AS ?streetAddress)
  (SAMPLE(?phoneValue) AS ?phone)
  (SAMPLE(?countryCodeValue) AS ?countryCode)
WHERE {
  ?item wdt:P31/wdt:P279* wd:Q133215;
        wdt:P625 ?coordValue.
  OPTIONAL { ?item wdt:P856 ?websiteValue. }
  OPTIONAL { ?item wdt:P571 ?inceptionValue. }
  OPTIONAL {
    ?item wdt:P137 ?operatorValue.
    ?operatorValue rdfs:label ?operatorLabelValue.
    FILTER(LANG(?operatorLabelValue) = "en")
  }
  OPTIONAL {
    ?item wdt:P6375 ?streetAddressValue.
    FILTER(LANG(?streetAddressValue) = "en" || LANG(?streetAddressValue) = "")
  }
  OPTIONAL { ?item wdt:P1329 ?phoneValue. }
  OPTIONAL {
    ?item wdt:P17 ?country.
    ?country wdt:P297 ?countryCodeValue.
  }
  SERVICE wikibase:label {
    bd:serviceParam wikibase:language "en,mul".
    ?item rdfs:label ?itemLabelValue.
  }
}
GROUP BY ?item
ORDER BY ?item
LIMIT ${limit}
OFFSET ${offset}`;
}

function parsePoint(value?: string) {
  const match = value?.match(/^Point\(([-+0-9.eE]+)\s+([-+0-9.eE]+)\)$/);
  if (!match) return null;
  const longitude = Number(match[1]);
  const latitude = Number(match[2]);
  if (!Number.isFinite(longitude) || !Number.isFinite(latitude)) return null;
  if (longitude < -180 || longitude > 180 || latitude < -90 || latitude > 90) {
    return null;
  }
  return { longitude, latitude };
}

function parseDate(value?: string) {
  if (!value) return null;
  const match = value.match(/([0-9]{4}-[0-9]{2}-[0-9]{2})/);
  return match?.[1] ?? null;
}

function toSourceRecord(binding: SparqlBinding) {
  const point = parsePoint(binding.coord?.value);
  const externalId = binding.item?.value.split("/").pop();
  const name = binding.itemLabel?.value?.trim();
  if (!point || !externalId || !/^Q[1-9][0-9]*$/.test(externalId) || !name) {
    return null;
  }

  return {
    source_name: "wikidata",
    external_id: externalId,
    external_url: `https://www.wikidata.org/wiki/${externalId}`,
    name,
    country_code: binding.countryCode?.value?.toUpperCase() || null,
    longitude: point.longitude,
    latitude: point.latitude,
    website_url: binding.website?.value || null,
    opened_on: parseDate(binding.inception?.value),
    operator_name: binding.operatorLabel?.value?.trim() || null,
    street_address: binding.streetAddress?.value?.trim() || null,
    phone: binding.phone?.value?.trim() || null,
    raw_payload: binding,
    fetched_at: new Date().toISOString(),
  };
}

function errorMessage(error: unknown) {
  if (error instanceof Error) return error.message;
  if (typeof error === "string") return error;
  try {
    return JSON.stringify(error);
  } catch {
    return "Unknown enrichment error";
  }
}

function delay(milliseconds: number) {
  return new Promise((resolve) => setTimeout(resolve, milliseconds));
}

async function fetchWikidata(query: string) {
  const attempts: Array<"POST" | "GET"> = ["POST", "POST", "GET"];
  const failures: string[] = [];

  for (const [index, method] of attempts.entries()) {
    if (index > 0) await delay(index === 1 ? 1_000 : 3_000);

    try {
      const url = method === "GET"
        ? `${WIKIDATA_ENDPOINT}?${new URLSearchParams({
          query,
          format: "json",
        })}`
        : WIKIDATA_ENDPOINT;
      const response = await fetch(url, {
        method,
        headers: {
          Accept: "application/sparql-results+json",
          ...(method === "POST"
            ? {
              "Content-Type": "application/x-www-form-urlencoded;charset=UTF-8",
            }
            : {}),
          "User-Agent": USER_AGENT,
        },
        body: method === "POST"
          ? new URLSearchParams({ query, format: "json" })
          : undefined,
        signal: AbortSignal.timeout(30_000),
      });
      if (response.ok) {
        return await response.json() as {
          results?: { bindings?: SparqlBinding[] };
        };
      }

      const detail = (await response.text()).replace(/\s+/g, " ").slice(0, 240);
      failures.push(
        `${method} HTTP ${response.status}${detail ? `: ${detail}` : ""}`,
      );
      if (![429, 500, 502, 503, 504].includes(response.status)) break;
    } catch (error) {
      failures.push(`${method}: ${errorMessage(error)}`);
    }
  }

  throw new Error(
    `Wikidata request failed after ${failures.length} attempts (${
      failures.join(" | ")
    })`,
  );
}

Deno.serve(async (request: Request) => {
  if (request.method !== "POST") {
    return jsonResponse({ error: "Method not allowed" }, 405);
  }

  const supabaseUrl = Deno.env.get("SUPABASE_URL") ?? "";
  const secretKey = getSecretKey();
  if (!supabaseUrl || !secretKey) {
    return jsonResponse({ error: "Server configuration unavailable" }, 500);
  }

  const admin = createClient(supabaseUrl, secretKey, {
    auth: {
      persistSession: false,
      autoRefreshToken: false,
      detectSessionInUrl: false,
    },
  });

  const token = request.headers.get("x-enrichment-token") ?? "";
  const { data: authorized, error: tokenError } = await admin.rpc(
    "validate_enrichment_cron_token",
    { candidate: token },
  );
  if (tokenError || authorized !== true) {
    return jsonResponse({ error: "Unauthorized" }, 401);
  }

  const { data: claimed, error: claimError } = await admin.rpc(
    "claim_wikidata_enrichment_batch",
  );
  if (claimError) return jsonResponse({ error: claimError.message }, 500);
  const batch = (claimed as ClaimedBatch[] | null)?.[0];
  if (!batch) return jsonResponse({ ok: true, idle: true });

  try {
    const query = sparqlQuery(batch.requested_batch_size, batch.batch_cursor);
    const payload = await fetchWikidata(query);
    const bindings = payload.results?.bindings ?? [];
    const records = bindings.map(toSourceRecord).filter((record) =>
      record !== null
    );
    if (records.length) {
      const { error: upsertError } = await admin
        .from("casino_enrichment_source_records")
        .upsert(records, { onConflict: "source_name,external_id" });
      if (upsertError) throw upsertError;
    }

    const { error: finishError } = await admin.rpc(
      "finish_wikidata_enrichment_batch",
      {
        processed_cursor: batch.batch_cursor,
        fetched_count: bindings.length,
        has_more: bindings.length === batch.requested_batch_size,
        error_message: null,
      },
    );
    if (finishError) throw finishError;

    return jsonResponse({
      ok: true,
      source: "wikidata",
      cursor: batch.batch_cursor,
      fetched: bindings.length,
      accepted: records.length,
      complete: bindings.length < batch.requested_batch_size,
    });
  } catch (error) {
    const message = errorMessage(error);
    await admin.rpc("finish_wikidata_enrichment_batch", {
      processed_cursor: batch.batch_cursor,
      fetched_count: 0,
      has_more: true,
      error_message: message,
    });
    return jsonResponse({ error: message }, 502);
  }
});
