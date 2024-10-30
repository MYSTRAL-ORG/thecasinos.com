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

class generateSiteMapCasino extends Command
{
    const SITEMAP_CASINOS_XML = 'sitemap.xml';

    protected $signature = 'app:sitemap';
    protected $description = 'Generate sitemap.xml';

    public function handle(): void
    {
        $sitemap = SitemapGenerator::create("")->getSitemap();

        // Pages statiques avec priorités
        $staticPages = [
            '' => 1.0,
            '/online' => 0.7,
            '/about' => 0.6,
            '/terms' => 0.6,
            '/policy' => 0.6
        ];

        foreach ($staticPages as $path => $priority) {
            $this->addOptimizedUrl($sitemap, $path, $priority);
        }

        // Casinos en ligne
        CasinoOnline::select('nom_casino_slug')
            ->chunk(1000, function ($casinos) use ($sitemap) {
                foreach ($casinos as $casino) {
                    $this->addOptimizedUrl(
                        $sitemap,
                        "/online/{$casino->nom_casino_slug}",
                        0.7,
                        'weekly'
                    );
                }
            });

        // Casinos physiques
        Casino::select('country_title', 'city_title', 'slug')
            ->chunk(1000, function ($casinos) use ($sitemap) {
                foreach ($casinos as $casino) {
                    $this->addOptimizedUrl(
                        $sitemap,
                        "/{$casino->country_title}/{$casino->city_title}/{$casino->slug}",
                        0.8,
                        'weekly',
                        new DateTime('now')
                    );
                }
            });

        // Catégories pays
        Category::select('country_title')
            ->chunk(1000, function ($categories) use ($sitemap) {
                foreach ($categories as $category) {
                    $this->addOptimizedUrl(
                        $sitemap,
                        "/{$category->country_title}",
                        0.7,
                        'weekly'
                    );
                }
            });

        // Catégories villes
        CategoryCity::select('country_title', 'city_title')
            ->chunk(1000, function ($cities) use ($sitemap) {
                foreach ($cities as $city) {
                    $this->addOptimizedUrl(
                        $sitemap,
                        "/{$city->country_title}/{$city->city_title}",
                        0.7,
                        'weekly'
                    );
                }
            });

        $this->writeSiteMap(public_path(self::SITEMAP_CASINOS_XML), $sitemap);
    }

    private function addOptimizedUrl(
        Sitemap $sitemap,
        string $path,
        float $priority,
        string $freq = 'monthly',
        ?DateTime $lastMod = null
    ): void {
        $url = Url::create(config('app.url') . $path)
            ->setChangeFrequency($freq)
            ->setPriority($priority)
            ->setLastModificationDate($lastMod ?? new DateTime('now'));

        $sitemap->add($url);
    }

    private function writeSiteMap(string $sitemapPath, Sitemap $sitemap): void
    {
        if (file_exists($sitemapPath)) {
            unlink($sitemapPath);
        }
        $sitemap->writeToFile($sitemapPath);
    }
}