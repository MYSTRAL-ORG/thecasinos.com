<?php

namespace App\Console\Commands;

use App\Models\Casino;
use App\Models\CasinoOnline;
use App\Models\Category;
use App\Models\CategoryCity;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapIndex;
use Spatie\Sitemap\Tags\Url;
use Carbon\Carbon;

class GenerateSiteMapCasino extends Command
{
    protected $signature = 'app:sitemap';
    protected $description = 'Generate segmented sitemaps with optimization for casino pages';

    private const SITEMAPS_DIR = 'sitemaps';
    private const CHUNK_SIZE = 1000;
    private const CACHE_DURATION = 1440; // 24 heures en minutes
    private DateTime $currentDate;

    public function __construct()
    {
        parent::__construct();
        $this->currentDate = new DateTime('now');
    }

    public function handle(): void
    {
        $this->info('Starting sitemap generation...');

        if (!file_exists(public_path(self::SITEMAPS_DIR))) {
            mkdir(public_path(self::SITEMAPS_DIR), 0755, true);
        }

        try {
            $this->generateSitemapsInParallel();
            $this->generateSitemapIndex();

            $this->info('Sitemap generation completed successfully!');
        } catch (\Exception $e) {
            $this->error('Error during sitemap generation: ' . $e->getMessage());
            \Log::error('Sitemap generation failed: ' . $e->getMessage());
        }
    }

    private function generateSitemapsInParallel(): void
    {
        $methods = [
            'generateStaticSitemap',
            'generateCasinosSitemap',
            'generateOnlineCasinosSitemap',
            'generateCategoriesSitemap'
        ];

        foreach ($methods as $method) {
            $this->$method();
        }
    }

    private function generateStaticSitemap(): void
    {
        $cacheKey = $this->getCacheKey('static');

        if (Cache::has($cacheKey)) {
            $sitemap = Cache::get($cacheKey);
        } else {
            $sitemap = Sitemap::create();

            $staticPages = [
                '' => ['priority' => 0.9, 'freq' => 'monthly'],
                '/online' => ['priority' => 0.8, 'freq' => 'monthly'],
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

            Cache::put($cacheKey, $sitemap, Carbon::now()->addMinutes(self::CACHE_DURATION));
        }

        $this->writeSitemapWithBackup('static.xml', $sitemap);
    }

    private function generateCasinosSitemap(): void
    {
        $cacheKey = $this->getCacheKey('casinos');

        if (Cache::has($cacheKey)) {
            $sitemap = Cache::get($cacheKey);
        } else {
            $sitemap = Sitemap::create();

            Casino::query()
                ->select('country_title', 'city_title', 'slug')
                ->lazy(self::CHUNK_SIZE)
                ->each(function ($casino) use ($sitemap) {
                    $this->addOptimizedUrl(
                        $sitemap,
                        "/{$casino->country_title}/{$casino->city_title}/{$casino->slug}",
                        0.7,
                        'monthly'
                    );
                });

            Cache::put($cacheKey, $sitemap, Carbon::now()->addMinutes(self::CACHE_DURATION));
        }

        $this->writeSitemapWithBackup('casinos.xml', $sitemap);
    }

    private function generateOnlineCasinosSitemap(): void
    {
        $cacheKey = $this->getCacheKey('online_casinos');

        if (Cache::has($cacheKey)) {
            $sitemap = Cache::get($cacheKey);
        } else {
            $sitemap = Sitemap::create();

            CasinoOnline::query()
                ->select('nom_casino_slug')
                ->lazy(self::CHUNK_SIZE)
                ->each(function ($casino) use ($sitemap) {
                    $this->addOptimizedUrl(
                        $sitemap,
                        "/online/{$casino->nom_casino_slug}",
                        0.7,
                        'monthly'
                    );
                });

            Cache::put($cacheKey, $sitemap, Carbon::now()->addMinutes(self::CACHE_DURATION));
        }

        $this->writeSitemapWithBackup('online-casinos.xml', $sitemap);
    }

    private function generateCategoriesSitemap(): void
    {
        $cacheKey = $this->getCacheKey('categories');

        if (Cache::has($cacheKey)) {
            $sitemap = Cache::get($cacheKey);
        } else {
            $sitemap = Sitemap::create();

            Category::query()
                ->select('country_title')
                ->lazy(self::CHUNK_SIZE)
                ->each(function ($category) use ($sitemap) {
                    $this->addOptimizedUrl(
                        $sitemap,
                        "/{$category->country_title}",
                        0.6,
                        'monthly'
                    );
                });

            CategoryCity::query()
                ->select('country_title', 'city_title')
                ->lazy(self::CHUNK_SIZE)
                ->each(function ($city) use ($sitemap) {
                    $this->addOptimizedUrl(
                        $sitemap,
                        "/{$city->country_title}/{$city->city_title}",
                        0.6,
                        'monthly'
                    );
                });

            Cache::put($cacheKey, $sitemap, Carbon::now()->addMinutes(self::CACHE_DURATION));
        }

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
        try {
            $url = Url::create(config('app.url') . $path)
                ->setChangeFrequency($freq)
                ->setPriority($priority)
                ->setLastModificationDate($this->currentDate);

            $sitemap->add($url);
        } catch (\Exception $e) {
            $this->error("Error adding URL {$path}: " . $e->getMessage());
            \Log::error("Sitemap generation error for {$path}: " . $e->getMessage());
        }
    }

    private function writeSitemapWithBackup($filename, $sitemap, bool $isIndex = false): void
    {
        try {
            $path = $isIndex ? public_path($filename) : $this->getSitemapPath($filename);

            if (file_exists($path)) {
                $backup = $path . '.bak';
                if (file_exists($backup)) {
                    unlink($backup);
                }
                rename($path, $backup);
            }

            $sitemap->writeToFile($path);
            $this->info("Generated {$filename} successfully");
        } catch (\Exception $e) {
            $this->error("Error generating {$filename}: " . $e->getMessage());
            \Log::error("Sitemap generation error for {$filename}: " . $e->getMessage());
        }
    }

    private function getCacheKey(string $type): string
    {
        return 'sitemap_' . $type . '_' . md5($this->currentDate->format('Y-m-d'));
    }

    private function getSitemapPath(string $filename): string
    {
        return public_path(self::SITEMAPS_DIR . '/' . $filename);
    }
}