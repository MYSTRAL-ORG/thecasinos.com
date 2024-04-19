<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Models\Url2Index;

// Include the Url2Index model

class InsertUrlsFromSitemap extends Command
{
    protected $signature = 'sitemap:insertUrl {url}';
    protected $description = 'InsertURLs from a sitemap into the database with a default status of false';

    public function handle()
    {
        $sitemapUrl = $this->argument('url');
        $this->info("Fetching URLs from: $sitemapUrl");

        // Fetch the sitemap
        $client = new Client();
        $response = $client->get($sitemapUrl);
        $body = $response->getBody()->getContents();

        // Parse XML
        $xml = simplexml_load_string($body);
        $count = 0;

        foreach ($xml->url as $urlElement) {
            // Create a new Url2Index instance for each URL and save it to the database with status false
            $url2Index = new Url2Index([
                'url' => (string)$urlElement->loc,
                'status' => false
            ]);
            $url2Index->save();
            $count++;
        }

        $this->info("Inserted " . $count . " URLs into the database.");
    }
}
