<?php

namespace App\Console\Commands;

use App\Models\Url2Index;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class IndexUrlsBing extends Command
{
    protected $signature = 'index:urlsBing';
    protected $description = 'Index URLs using Bing Indexing API and update status for pending URLs';

    public function handle()
    {
        $filePath = public_path('seo-bing.txt');
        file_put_contents($filePath, null);
        $pendingUrls = Url2Index::where('status_bing', false)->get();

        if ($pendingUrls->isEmpty()) {
            $this->writeLog($filePath, "No pending URLs to process.");
            return;
        }

        $http = $this->setupHttpClient();
        $totalUrls = $pendingUrls->count();
        $indexedCount = 0;

        foreach ($pendingUrls as $urlEntry) {
            if ($this->indexURL($http, $urlEntry->url, $filePath)) {
                $urlEntry->status_bing = true;
                $urlEntry->save();
                $indexedCount++;
            }
        }

        $this->logSummary($filePath, $totalUrls, $indexedCount);
    }

    private function indexURL($http, $url, $filePath)
    {
        try {
            $response = $http->post('https://api.indexnow.org/IndexNow', [
                'json' => [
                    'host' => 'www.thecasinos.com', // Modify as necessary
                    'key' => 'daed8e700b6544eb8660b633d80043df', // Your actual key
                    'keyLocation' => 'https://www.thecasinos.com/daed8e700b6544eb8660b633d80043df.txt', // Actual key location
                    'urlList' => [$url]
                ]
            ]);
            return $response->getStatusCode() === 200;
        } catch (Exception $e) {
            $this->writeLog($filePath, 'Failed to index URL: ' . $url . ' with error: ' . $e->getMessage());
            return false;
        }
    }

    private function setupHttpClient()
    {
        return new Client(); // GuzzleHttp Client
    }

    private function writeLog($filePath, $message)
    {
        $dateWithTimezone = Carbon::now(new \DateTimeZone('GMT+4'));
        file_put_contents($filePath, $dateWithTimezone->toDateTimeString() . " - " . $message . "\n", FILE_APPEND);
    }

    private function logSummary($filePath, $totalUrls, $indexedCount)
    {
        $remaining = $totalUrls - $indexedCount;
        $percentageIndexed = ($totalUrls > 0) ? round($indexedCount / $totalUrls * 100, 2) : 0;
        $summary = "Total URLs: $totalUrls, Indexed URLs: $indexedCount, Remaining: $remaining, Success Rate: $percentageIndexed%";
        $this->writeLog($filePath, $summary);
    }
}
