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
        $this->createAndAddUrl(config('app.url'),$sitemap );
        $this->createAndAddUrl(config('app.url').'/online',$sitemap );
        $this->createAndAddUrl(config('app.url').'/about',$sitemap );
        $this->createAndAddUrl(config('app.url').'/terms',$sitemap );
        $this->createAndAddUrl(config('app.url').'/policy',$sitemap );


        //CASINOS ONLINE

        CasinoOnline::all()->each(function (CasinoOnline $casinoOnLine) use ($sitemap) {
            $this->createAndAddUrl(config('app.url') . "/online/" .$casinoOnLine->nom_casino_slug ,$sitemap);
        });





        //CASINOS

        // Liste des casinos
        Casino::all()->each(function (Casino $casino) use ($sitemap) {
            $this->createAndAddUrl(config('app.url') . "/" . $casino->country_title . "/" . $casino->city_title . "/" . $casino->slug ,$sitemap);
        });


        Category::all()->each(function (Category $category) use ($sitemap) {
            $this->createAndAddUrl(config('app.url') . "/" . $category->country_title  ,$sitemap);
        });
        CategoryCity::all()->each(function (CategoryCity $categoryCity) use ($sitemap) {
            $this->createAndAddUrl(config('app.url') . "/" . $categoryCity->country_title . "/" . $categoryCity->city_title ,$sitemap);
        });



        $this->writeSiteMap($sitemapCasinoPath, $sitemap);














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
        $sitemap->writeToFile($sitemapPath);
        $gzPath = $sitemapPath . '.gz';
        $gzData = gzencode(file_get_contents($sitemapPath), 9);
        file_put_contents($gzPath, $gzData);



        // Enregistrez le sitemap dans un fichier public
        //
    }
}
