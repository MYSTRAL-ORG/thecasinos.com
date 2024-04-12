<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Google_Client;
use GuzzleHttp\Client;
use League\Csv\Reader;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class IndexUrls extends Command
{
    protected $signature = 'index:urls';
    protected $description = 'Index URLs using Google Indexing API';

    public function handle()
    {
        $directoryPath = storage_path('app/google/csv-2-load');
        $files = Storage::disk('local')->files('google/csv-2-load'); // Fetch files from the directory

        foreach ($files as $file) {

            if (substr($file, -4) === '.csv' && substr($file, -9) !== '.csv.done') {

                Log::info("***************************** Processing file: $file");
                $fullPath = storage_path('app/' . $file);
                $this->info("Processing file: $fullPath");

                if (!$this->processFile($fullPath)) {
                    $this->error("Stopped processing due to error in file: $fullPath");
                    return;
                }

                // Rename file if all URLs are processed successfully
                $newPath = $fullPath . '.done';
                Storage::disk('local')->move($file, 'google/csv-2-load/' . basename($newPath));
                $this->info("Renamed $fullPath to $newPath after successful processing.");
            }
        }
    }

    private function processFile($filePath)
    {
        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0);
        $urls = $csv->getRecords();

        $jsonKeyFile = storage_path("app/google/account1.json"); // Assuming using only one account for simplicity
        if (!file_exists($jsonKeyFile)) {
            $this->error("Error: {$jsonKeyFile} not found!");
            return false;
        }

        $http = $this->setupHttpClient($jsonKeyFile);

        foreach ($urls as $url) {
            if (!$this->indexURL($http, $url)) {
                return false; // Stop processing this file on error
            }
        }

        return true; // All URLs indexed successfully
    }

    private function indexURL($http, $url)
    {
        $content = [
            'json' => [
                'url' => trim($url['URL']),
                'type' => 'URL_UPDATED'
            ]
        ];

        try {
            $response = $http->post('v3/urlNotifications:publish', $content);
            $this->info('URL indexed successfully: ' . $url['URL']);
            return true;
        } catch (\Exception $e) {
            $this->error('Failed to index URL: ' . $url['URL'] . ' with error: ' . $e->getMessage());
            return false;
        }
    }

    private function setupHttpClient($jsonKeyFile)
    {
        $client = new Google_Client();
        $client->setAuthConfig($jsonKeyFile);
        $client->addScope('https://www.googleapis.com/auth/indexing');

        if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithAssertion();
        }

        $accessToken = $client->getAccessToken()['access_token'];

        $guzzleClient = new Client([
            'base_uri' => 'https://indexing.googleapis.com/',
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json'
            ]
        ]);

        return $guzzleClient;
    }
}
