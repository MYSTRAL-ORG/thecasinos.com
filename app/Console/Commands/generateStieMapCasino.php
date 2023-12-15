<?php

namespace App\Console\Commands;

use App\Models\Casino;
use App\Models\CasinoOnline;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;

class generateStieMapCasino extends Command
{
    const SITEMAP_CASINOS_XML = 'sitemap-casinos.xml';
    const SITEMAP_GLOBAL_XML = 'sitemap-global.xml';

    const SITEMAP_CASINOS_ONLINE_XML = 'sitemap-casinos-online.xml';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sitemap-casino';

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

        $sitemapPath = public_path(self::SITEMAP_GLOBAL_XML);
        $sitemapCasinoPath = public_path(self::SITEMAP_CASINOS_XML);
        $sitemapCasinoOnLinePath = public_path(self::SITEMAP_CASINOS_ONLINE_XML);



        //GLOBAL
        $sitemap = SitemapGenerator::create("")->getSitemap();
        $this->createAndAddUrl(config('app.url'),$sitemap );
        $this->createAndAddUrl(config('app.url').'/online',$sitemap );
        $this->createAndAddUrl(config('app.url').'/about',$sitemap );
        $this->createAndAddUrl(config('app.url').'/terms',$sitemap );
        $this->createAndAddUrl(config('app.url').'/policy',$sitemap );
        $this->writeSiteMap($sitemapPath, $sitemap);



        //CASINOS
        $sitemapCasino = SitemapGenerator::create("")->getSitemap();
        // Liste des casinos
        Casino::all()->each(function (Casino $casino) use ($sitemapCasino) {
           $this->createAndAddUrl(config('app.url') . "/" . $casino->country_title . "/" . $casino->city_title . "/" . $casino->slug ,$sitemapCasino);
        });
        $this->writeSiteMap($sitemapCasinoPath, $sitemapCasino);



        //CASINOS ONLINE
        $sitemapCasinoOnLine = SitemapGenerator::create("")->getSitemap();
        // Liste des casinos
        CasinoOnline::all()->each(function (CasinoOnline $casinoOnLine) use ($sitemapCasinoOnLine) {
            $this->createAndAddUrl(config('app.url') . "/" .$casinoOnLine->nom_casino_slug ,$sitemapCasinoOnLine);
        });
        $this->writeSiteMap($sitemapCasinoOnLinePath, $sitemapCasinoOnLine);


    }

    /**
     * @param Casino $casino
     * @return Url
     */
    function createAndAddUrl(String $url, Sitemap $sitemap): void
    {
        $url = new Url($url);
        $url->setChangeFrequency('daily');
        $url->setLastModificationDate(new DateTime('now'));
        $url->setPriority(1);
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

        // Enregistrez le sitemap dans un fichier public
        $sitemap->writeToFile($sitemapPath);
    }
}
