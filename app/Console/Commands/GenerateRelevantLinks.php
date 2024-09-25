<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Casino;
use Illuminate\Support\Facades\Http;
use Log;

class GenerateRelevantLinks extends Command
{
    protected $signature = 'generate:relevant-links';
    protected $description = 'Generate relevant internal links for casinos from sitemap';

    public function handle()
    {
        // Step 1: Fetch and parse the sitemap
        $sitemapUrl = 'https://thecasinos.com/sitemap.xml'; // Replace with the actual sitemap URL
        $sitemapXml = Http::get($sitemapUrl)->body();
        $sitemapCasinos = $this->parseSitemap($sitemapXml);

        $casinos = Casino::all(); // Fetch all casinos
        $this->info(print_r($sitemapCasinos, true));
        foreach ($casinos as $casino) {
            // Step 2: Find a relevant casino from the same country but a different city
            $relevantCasino = Casino::where('id', '!=', $casino->id)
                ->where('country_title', $casino->country_title) ->inRandomOrder() // Random selection
                ->first();



            // Step 3: Check if the relevant casino exists in the sitemap
            if ($relevantCasino && in_array($this->generateCasinoUrl($relevantCasino ,true), $sitemapCasinos)) {
                $this->info("yo");
                // If the casino exists in the sitemap, generate the internal link
                $internalLink = $this->generateCasinoUrl($relevantCasino, false);

                // Update the current casino with the relevant internal link
                $casino->update([
                    'link_interne_id' => $relevantCasino->id
                ]);
            }
        }

        $this->info('Relevant internal links generated successfully.');
    }

    /**
     * Parse the sitemap XML and extract casino URLs
     */
    private function parseSitemap($sitemapXml)
    {
        $xml = simplexml_load_string($sitemapXml);
        $sitemapCasinos = [];

        foreach ($xml->url as $url) {
            $loc = (string) $url->loc;
            $sitemapCasinos[] = $loc;
        }

        return $sitemapCasinos;
    }

    /**
     * Generate a casino URL based on country, city, and name
     */
    private function generateCasinoUrl($casino ,$full = false)
    {
        return ($full ? "https://www.thecasinos.com" : "" ). '/' . $casino->country_title . '/' . $casino->city_title . '/' . $casino->slug;
    }
}
