<?php

namespace App\Console\Commands;

use App\Models\Casino;
use App\Models\CasinoOnline;
use App\Models\Category;
use App\Models\CategoryCity;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;

class generateStieMapCasino extends Command
{
    const SITEMAP_CASINOS_XML = 'sitemap';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sitemap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate sitemap.xml';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {$urls = collect([]);

// Ajouter des URLs globales
        $globalUrls = [
            config('app.url'),
            config('app.url').'/online',
            config('app.url').'/about',
            config('app.url').'/terms',
            config('app.url').'/policy',
        ];
        foreach ($globalUrls as $url) {
            $urls->push(['url' => $url, 'priority' => 0.5]);
        }

// Ajouter des URLs pour CasinoOnline
        CasinoOnline::all()->each(function($item) use ($urls) {
            $urls->push(['url' => config('app.url') . "/online/" . $item->nom_casino_slug, 'priority' => 0.5]);
        });

// Ajouter des URLs pour Casino avec une priorité élevée
        Casino::all()->each(function($item) use ($urls) {
            $urls->push(['url' => config('app.url') . "/" . $item->country_title . "/" . $item->city_title . "/" . $item->slug, 'priority' => 1.0]);
        });

// Ajouter des URLs pour Category et CategoryCity
        Category::all()->each(function($item) use ($urls) {
            $urls->push(['url' => config('app.url') . "/" . $item->country_title, 'priority' => 0.5]);
        });
        CategoryCity::all()->each(function($item) use ($urls) {
            $urls->push(['url' => config('app.url') . "/" . $item->country_title . "/" . $item->city_title, 'priority' => 0.5]);
        });

// Segmenter et générer des sitemaps
        // Segmenter et générer des sitemaps
        $urls->chunk(1500)->each(function ($chunk, $index) {
            $sitemap = Sitemap::create();
            foreach ($chunk as $item) {
                $this->createAndAddUrl($item['url'], $sitemap, $item['priority']);
            }

            $filename = $index === 0 ? 'sitemap.xml' : "sitemap-{$index}.xml";
            $sitemap->writeToFile(public_path($filename));
        });

    }

    function createAndAddUrl(String $url, Sitemap $sitemap, float $priority = 0.5): void
    {
        $url = Url::create($url)
            ->setChangeFrequency('weekly') // ou 'daily' selon la fréquence de mise à jour de votre contenu
            ->setLastModificationDate(now())
            ->setPriority($priority);
        $sitemap->add($url);
    }



    /**
     * @param string $sitemapPath
     * @param Sitemap $sitemap
     * @return void
     */
    public function writeSiteMap(string $sitemapPath, Sitemap $sitemap): void
    {
// Vérifiez si le fichier sitemap existe déjà et le remplacer si nécessaire
        if (file_exists($sitemapPath)) {
            unlink($sitemapPath);
        }
        $sitemap->writeToFile($sitemapPath);
        $gzPath = $sitemapPath . '.gz';
        $gzData = gzencode(file_get_contents($sitemapPath), 9);
        file_put_contents($gzPath, $gzData);



        // Enregistrez le sitemap dans un fichier public
        //
    }
}
