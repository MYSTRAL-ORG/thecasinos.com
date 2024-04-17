<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Google_Client;
use GuzzleHttp\Client;
use League\Csv\Reader;
use League\Csv\Writer;
use Illuminate\Support\Facades\Storage;
use Log;

class IndexUrls extends Command
{
    protected $signature = 'index:urls';
    protected $description = 'Index URLs using Google Indexing API and update status for pending URLs';

    public function handle()
    {
        
        Log::info("Processing  $this->description");
        $filePath = storage_path('app/google/data.csv');

        if (!file_exists($filePath)) {
            $this->logError("File not found: $filePath");
            $this->error("File not found: $filePath");
            return;
        }

        Log::info("Processing file: $filePath");

        $result = $this->processFile($filePath);
        if (!$result['success']) {
            $this->logSummary($result['processed'], $result['remaining'], $result['percentage']); // Log summary before error
            Log::info("Processing stopped due to an error: " . $result['error']);
            $this->logError("Processing stopped due to an error: " . $result['error']);
            return;
        }

        Log::info("Successfully processed the file.");
        $this->logSummary($result['processed'], $result['remaining'], $result['percentage']);
    }

    private function processFile($filePath)
    {
        $processedCount = 0;
        $remaining = 0;
        $percentageComplete = 0;

        try {
            $csv = Reader::createFromPath($filePath, 'r');
            $csv->setHeaderOffset(0);
            $records = iterator_to_array($csv->getRecords());
            $initialTotalPendingUrls = count(array_filter($records, function ($record) {
                return $record['Status'] == '0';
            }));

            $remaining = $initialTotalPendingUrls;  // Initial value before processing

            $http = $this->setupHttpClient(storage_path("app/google/account1.json"));

            foreach ($records as &$record) {
                if ($record['Status'] == '0') {
                    $indexResult = $this->indexURL($http, $record['URL']);
                    if (!$indexResult) {
                        Log::error("Failed to index URL: {$record['URL']}");
                        break;
                    }
                    $record['Status'] = '1';
                    $processedCount++;
                    $remaining--;
                }
            }

            // Rewrite the CSV with updated statuses
            $writer = Writer::createFromPath($filePath, 'w');
            $writer->insertOne(['URL', 'Status']);
            $writer->insertAll($records);

            // Recalculate the percentage of URLs with status '1' after writing to CSV
            $totalSuccessUrls = count(array_filter($records, function ($record) {
                return $record['Status'] == '1';
            }));
            $totalUrls = count($records);
            $percentageComplete = ($totalUrls > 0) ? round($totalSuccessUrls / $totalUrls * 100, 2) : 0;

            return [
                'success' => true,
                'processed' => $processedCount,
                'remaining' => $initialTotalPendingUrls - $processedCount,
                'percentage' => $percentageComplete
            ];

        } catch (\Exception $e) {
            Log::error("An error occurred: " . $e->getMessage());
            return [
                'success' => false,
                'processed' => $processedCount,
                'remaining' => count($records) - $processedCount,  // Calculate remaining based on unprocessed entries
                'percentage' => 0,  // Set to 0 in case of exception
                'error' => $e->getMessage()
            ];
        }
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
        //log summary
        $logMessage = now()->toDateTimeString() . " - Processed: $processed, Total not Processed: $remaining, Percentage: $percentage%\n";
        file_put_contents(public_path('seo.txt'), $logMessage, FILE_APPEND);  // Utilisation de file_put_contents avec public_path()
    }

    private function logError($errorMessage)
    {
        $logMessage = now()->toDateTimeString() . " - ERROR: $errorMessage\n";
        file_put_contents(public_path('seo.txt'), $logMessage, FILE_APPEND);  // Utilisation de file_put_contents avec public_path()
    }
}
