<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Google_Client;
use GuzzleHttp\Client;
use League\Csv\Reader;
use League\Csv\Writer;
use Illuminate\Support\Facades\Storage;

class IndexUrls extends Command
{
    protected $signature = 'index:urls';
    protected $description = 'Index URLs using Google Indexing API and update status for pending URLs';

    public function handle()
    {
        $filePath = storage_path('app/google/data.csv');

        if (!file_exists($filePath)) {
            $this->logError("File not found: $filePath");
            $this->error("File not found: $filePath");
            return;
        }

        $this->info("Processing file: $filePath");

        $result = $this->processFile($filePath);
        if (!$result['success']) {
            $this->logSummary($result['processed'], $result['remaining'], $result['percentage']); // Log summary before error
            $this->error("Processing stopped due to an error: " . $result['error']);
            $this->logError("Processing stopped due to an error: " . $result['error']);
            return;
        }

        $this->info("Successfully processed the file.");
        $this->logSummary($result['processed'], $result['remaining'], $result['percentage']);
    }

    private function processFile($filePath)
    {
        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0);
        $records = iterator_to_array($csv->getRecords());
        $processedCount = 0;
        $initialTotalPendingUrls = count(array_filter($records, function ($record) {
            return $record['Status'] == '0';
        }));

        $jsonKeyFile = storage_path("app/google/account1.json");
        if (!file_exists($jsonKeyFile)) {
            $this->logError("JSON key file not found: {$jsonKeyFile}");
            return ['success' => false, 'error' => "JSON key file not found: {$jsonKeyFile}", 'processed' => $processedCount, 'remaining' => $initialTotalPendingUrls, 'percentage' => 0];
        }

        $http = $this->setupHttpClient($jsonKeyFile);

        foreach ($records as &$record) {
            if ($record['Status'] == '0') {
                if (!$this->indexURL($http, $record['URL'])) {
                    $remaining = $initialTotalPendingUrls - $processedCount;
                    $percentageComplete = ($initialTotalPendingUrls > 0) ? round($processedCount / $initialTotalPendingUrls * 100, 2) : 0;
                    return ['success' => false, 'error' => "Failed to index URL: {$record['URL']}", 'processed' => $processedCount, 'remaining' => $remaining, 'percentage' => $percentageComplete];
                }
                $record['Status'] = 1;
                $processedCount++;
            }
        }

        $remaining = $initialTotalPendingUrls - $processedCount;
        $percentageComplete = ($initialTotalPendingUrls > 0) ? round($processedCount / $initialTotalPendingUrls * 100, 2) : 0;

        // Rewrite the CSV with updated statuses
        $writer = Writer::createFromPath($filePath, 'w');
        $writer->insertOne(['URL', 'Status']);
        $writer->insertAll($records);

        return ['success' => true, 'processed' => $processedCount, 'remaining' => $remaining, 'percentage' => $percentageComplete];
    }

    private function indexURL($http, $url)
    {
        $content = [
            'json' => [
                'url' => trim($url),
                'type' => 'URL_UPDATED'
            ]
        ];

        try {
            $response = $http->post('v3/urlNotifications:publish', $content);
            return true;
        } catch (\Exception $e) {
            $this->logError('Failed to index URL: ' . $url . ' with error: ' . $e->getMessage());
            return false;
        }
    }

    private function setupHttpClient($jsonKeyFile)
    {
        $client = new Google_Client();
        $client->setAuthConfig($jsonKeyFile);
        $client->addScope('https://www.googleapis.com/auth/indexing');
        $client->fetchAccessTokenWithAssertion();

        $accessToken = $client->getAccessToken()['access_token'];

        return new Client([
            'base_uri' => 'https://indexing.googleapis.com/',
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json'
            ]
        ]);
    }

    private function logSummary($processed, $remaining, $percentage)
    {
        $logMessage = now()->toDateTimeString() . " - Processed: $processed, Total not Processed: $remaining, Percentage: $percentage%\n";
        file_put_contents(public_path('seo.txt'), $logMessage, FILE_APPEND);  // Utilisation de file_put_contents avec public_path()
    }

    private function logError($errorMessage)
    {
        $logMessage = now()->toDateTimeString() . " - ERROR: $errorMessage\n";
        file_put_contents(public_path('seo.txt'), $logMessage, FILE_APPEND);  // Utilisation de file_put_contents avec public_path()
    }
}
