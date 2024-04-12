<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use League\Csv\Writer;
use SplTempFileObject;

class GenerateCsvFromSitemap extends Command
{
    protected $signature = 'sitemap:generate-csv {url}';
    protected $description = 'Generate CSV files from a sitemap with a maximum of 200 URLs each';

    public function handle()
    {
        $sitemapUrl = $this->argument('url');
        $this->info("Fetching URLs from: $sitemapUrl");

        // Fetch the sitemap
        $client = new Client();
        $response = $client->get($sitemapUrl);
        $body = $response->getBody()->getContents();

        // Parse XML
        $urls = [];
        $xml = simplexml_load_string($body);

        foreach ($xml->url as $urlElement) {
            $urls[] = (string)$urlElement->loc;
        }

        // Divide URLs into chunks and save to CSV
        $chunks = array_chunk($urls, 200);

        foreach ($chunks as $index => $chunk) {
            $csvFilePath = storage_path("app/google/csv-2-load/data_{$index}.csv"); // Using the public folder inside storage
            $csv = Writer::createFromPath($csvFilePath, 'w+'); // Ensure the path is writable
            $csv->insertOne(['URL']);
            $csv->insertAll(array_map(fn($url) => [$url], $chunk));

            $this->info("Generated: {$csvFilePath} with " . count($chunk) . " URLs");
        }
    }
}
