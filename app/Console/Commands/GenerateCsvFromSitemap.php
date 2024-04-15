<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use League\Csv\Writer;
use SplTempFileObject;

class GenerateCsvFromSitemap extends Command
{
    protected $signature = 'sitemap:generate-csv {url}';
    protected $description = 'Generate a CSV file from a sitemap, with each URL having a default status of 0';

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
            $urls[] = [(string)$urlElement->loc, 0]; // Include status with default 0
        }

        // Create CSV file in storage
        $csvFilePath = storage_path("app/google/data.csv");
        $csv = Writer::createFromPath($csvFilePath, 'w+'); // Ensure the path is writable
        $csv->insertOne(['URL', 'Status']); // Define columns with Status
        $csv->insertAll($urls); // Insert all URLs with status

        $this->info("Generated: {$csvFilePath} with " . count($urls) . " URLs");
    }
}
