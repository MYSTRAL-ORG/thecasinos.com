<?php

namespace App\Console\Commands;

use App\Models\Casino;
use App\Models\CasinoOnline;
use App\Models\Category;
use App\Models\CategoryCity;
use DateTime;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapIndex;
use Spatie\Sitemap\Tags\Url;

class GenerateSiteMapCasino extends Command
{
    protected $signature = 'app:sitemap';
    protected $description = 'Generate segmented sitemaps with optimization for casino pages';

    private const SITEMAPS_DIR = 'sitemaps';
    private const CHUNK_SIZE = 1000;
    private DateTime $currentDate;

    public function __construct()
    {
        parent::__construct();
        $this->currentDate = new DateTime('now');
    }

    public function handle(): void
    {
        $this->info('Starting sitemap generation...');

        // Créer le dossier sitemaps s'il n'existe pas
        if (!file_exists(public_path(self::SITEMAPS_DIR))) {
            mkdir(public_path(self::SITEMAPS_DIR), 0755, true);
        }

        // Générer les différents sitemaps
        $this->generateStaticSitemap();
        $this->generateCasinosSitemap();
        $this->generateOnlineCasinosSitemap();
        $this->generateCategoriesSitemap();

        // Générer l'index principal
        $this->generateSitemapIndex();

        $this->info('Sitemap generation completed successfully!');
    }

    private function generateStaticSitemap(): void
    {
        $sitemap = Sitemap::create();

        $staticPages = [
            '' => ['priority' => 1.0, 'freq' => 'daily'],
            '/online' => ['priority' => 0.9, 'freq' => 'daily'],
            '/about' => ['priority' => 0.5, 'freq' => 'monthly'],
            '/terms' => ['priority' => 0.5, 'freq' => 'monthly'],
            '/policy' => ['priority' => 0.5, 'freq' => 'monthly']
        ];

        foreach ($staticPages as $path => $config) {
            $this->addOptimizedUrl(
                $sitemap,
                $path,
                $config['priority'],
                $config['freq']
            );
        }

        $this->writeSitemapWithBackup('static.xml', $sitemap);
    }

    private function generateCasinosSitemap(): void
    {
        $sitemap = Sitemap::create();

        Casino::select('country_title', 'city_title', 'slug')
            ->chunk(self::CHUNK_SIZE, function ($casinos) use ($sitemap) {
                foreach ($casinos as $casino) {
                    $this->addOptimizedUrl(
                        $sitemap,
                        "/{$casino->country_title}/{$casino->city_title}/{$casino->slug}",
                        0.9, // Priorité maximale pour les casinos physiques
                        'daily' // Fréquence augmentée pour améliorer le crawl
                    );
                }
            });

        $this->writeSitemapWithBackup('casinos.xml', $sitemap);
    }

    private function generateOnlineCasinosSitemap(): void
    {
        $sitemap = Sitemap::create();

        CasinoOnline::select('nom_casino_slug')
            ->chunk(self::CHUNK_SIZE, function ($casinos) use ($sitemap) {
                foreach ($casinos as $casino) {
                    $this->addOptimizedUrl(
                        $sitemap,
                        "/online/{$casino->nom_casino_slug}",
                        0.8,
                        'daily'
                    );
                }
            });

        $this->writeSitemapWithBackup('online-casinos.xml', $sitemap);
    }

    private function generateCategoriesSitemap(): void
    {
        $sitemap = Sitemap::create();

        // Catégories pays
        Category::select('country_title')
            ->chunk(self::CHUNK_SIZE, function ($categories) use ($sitemap) {
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
            ->chunk(self::CHUNK_SIZE, function ($cities) use ($sitemap) {
                foreach ($cities as $city) {
                    $this->addOptimizedUrl(
                        $sitemap,
                        "/{$city->country_title}/{$city->city_title}",
                        0.7,
                        'weekly'
                    );
                }
            });

        $this->writeSitemapWithBackup('categories.xml', $sitemap);
    }

    private function generateSitemapIndex(): void
    {
        $sitemapIndex = SitemapIndex::create();

        $sitemaps = [
            'static.xml',
            'casinos.xml',
            'online-casinos.xml',
            'categories.xml'
        ];

        foreach ($sitemaps as $sitemap) {
            $sitemapIndex->add(url(self::SITEMAPS_DIR . '/' . $sitemap));
        }

        $this->writeSitemapWithBackup('sitemap.xml', $sitemapIndex, true);
    }

    private function addOptimizedUrl(
        Sitemap $sitemap,
        string $path,
        float $priority,
        string $freq
    ): void {
        $url = Url::create(config('app.url') . $path)
            ->setChangeFrequency($freq)
            ->setPriority($priority)
            ->setLastModificationDate($this->currentDate);

        $sitemap->add($url);
    }

    private function writeSitemapWithBackup($filename, $sitemap, bool $isIndex = false): void
    {
        $path = $isIndex ? public_path($filename) : $this->getSitemapPath($filename);

        // Créer une sauvegarde si le fichier existe déjà
        if (file_exists($path)) {
            $backup = $path . '.bak';
            if (file_exists($backup)) {
                unlink($backup);
            }
            rename($path, $backup);
        }

        if ($isIndex) {
            $sitemap->writeToFile($path);
        } else {
            $sitemap->writeToFile($path);
        }

        $this->info("Generated {$filename}");
    }

    private function getSitemapPath(string $filename): string
    {
        return public_path(self::SITEMAPS_DIR . '/' . $filename);
    }
}