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
    const SITEMAP_CASINOS_XML = 'sitemap.xml';

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
    {
        $sitemapCasinoPath = public_path(self::SITEMAP_CASINOS_XML);

        //GLOBAL
        $sitemap = SitemapGenerator::create("")->getSitemap();
        $this->createAndAddUrl(config('app.url'), $sitemap, 1);
        $this->createAndAddUrl(config('app.url') . '/online', $sitemap, 0.6);
        $this->createAndAddUrl(config('app.url') . '/about', $sitemap, 0.6);
        $this->createAndAddUrl(config('app.url') . '/terms', $sitemap, 0.6);
        $this->createAndAddUrl(config('app.url') . '/policy', $sitemap, 0.6);

        //CASINOS ONLINE
        CasinoOnline::all()->each(function (CasinoOnline $casinoOnLine) use ($sitemap) {
            $this->createAndAddUrl(config('app.url') . "/online/" . $casinoOnLine->nom_casino_slug, $sitemap, 0.6);
        });

        //CASINOS
        Casino::all()->each(function (Casino $casino) use ($sitemap) {
            $this->createAndAddUrl(config('app.url') . "/" . $casino->country_title . "/" . $casino->city_title . "/" . $casino->slug, $sitemap, 1);
        });

        Category::all()->each(function (Category $category) use ($sitemap) {
            $this->createAndAddUrl(config('app.url') . "/" . $category->country_title, $sitemap, 0.6);
        });
        CategoryCity::all()->each(function (CategoryCity $categoryCity) use ($sitemap) {
            $this->createAndAddUrl(config('app.url') . "/" . $categoryCity->country_title . "/" . $categoryCity->city_title, $sitemap, 0.6);
        });

        $this->writeSiteMap($sitemapCasinoPath, $sitemap);
    }

    /**
     * @param string $url
     * @param Sitemap $sitemap
     * @param float $priority
     */
    function createAndAddUrl(string $url, Sitemap $sitemap, float $priority): void
    {
        $url = new Url($url);
        $url->setChangeFrequency('daily');
        $url->setLastModificationDate(new DateTime('now'));
        $url->setPriority($priority);
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
        // Suppression de la création du fichier .gz pour la compression
    }
}
