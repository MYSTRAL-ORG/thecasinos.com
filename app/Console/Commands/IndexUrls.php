<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Google_Client;
use GuzzleHttp\Client;
use App\Models\Url2Index;

// Ensure this is the correct model name
use Exception;
use Log;

class IndexUrls extends Command
{
    protected $signature = 'index:urls';
    protected $description = 'Index URLs using Google Indexing API and update status for pending URLs';

    public function handle()
    {

        $filePath = public_path('seo.txt');
        file_put_contents($filePath, null);
        $pendingUrls = Url2Index::where('status', false)->get();

        if ($pendingUrls->isEmpty()) {
            $this->writeLog($filePath, "No pending URLs to process.");

            return;
        }

        $http = $this->setupHttpClient(storage_path("app/google/account1.json"));
        $totalUrls = $pendingUrls->count();
        $indexedCount = 0;

        foreach ($pendingUrls as $urlEntry) {
            $indexSuccess = $this->indexURL($http, $urlEntry->url, $filePath);
            if (!$indexSuccess) {
                $this->writeLog($filePath, "Processing stopped after failure to index URL: " . $urlEntry->url);
                break; // Stop processing further URLs upon failure
            }
            $urlEntry->status = true;
            $urlEntry->save();
            $indexedCount++;
        }

        // Get the total count of URLs from the database
        $totalUrls = Url2Index::count();

        // Get the count of indexed URLs (status = true)
        $indexedCount = Url2Index::where('status', true)->count();

        // Calculate remaining URLs (status = false)
        $remaining = Url2Index::where('status', false)->count();

        // Calculate the percentage of indexed URLs
        $percentageIndexed = ($totalUrls > 0) ? round($indexedCount / $totalUrls * 100, 2) : 0;

        $summary = "Total URLs: $totalUrls, Indexed URLs: $indexedCount, Remaining: $remaining, Success Rate: $percentageIndexed%";
        $this->writeLog($filePath, $summary);
    }

    private function indexURL($http, $url, $filePath)
    {
        try {
            $response = $http->post('v3/urlNotifications:publish', [
                'json' => [
                    'url' => trim($url),
                    'type' => 'URL_UPDATED'
                ]
            ]);
            return false;
        } catch (Exception $e) {
            $errorMessage = 'Failed to index URL: ' . $url . ' with error: ' . $e->getMessage();
            $this->writeLog($filePath, $errorMessage);
            return false;
        }
    }

    private function setupHttpClient($jsonKeyFile)
    {
        $client = new Google_Client();
        $client->setAuthConfig($jsonKeyFile);
        $client->addScope('https://www.googleapis.com/auth/indexing');
        $client->fetchAccessTokenWithAssertion();

        return new Client([
            'base_uri' => 'https://indexing.googleapis.com/',
            'headers' => [
                'Authorization' => 'Bearer ' . $client->getAccessToken()['access_token'],
                'Content-Type' => 'application/json'
            ]
        ]);
    }

    private function writeLog($filePath, $message)
    {
        // Create a Carbon instance for now in the GMT+4 timezone
        $dateWithTimezone = now()->timezone('GMT+4');

        // Use FILE_APPEND flag to ensure that each log entry is appended to the existing file
        file_put_contents($filePath, $dateWithTimezone->toDateTimeString() . " - " . $message . "\n", FILE_APPEND);
    }

}
