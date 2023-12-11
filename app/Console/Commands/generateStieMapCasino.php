<?php

namespace App\Console\Commands;

use App\Models\Casino;
use Illuminate\Console\Command;
use Spatie\Sitemap\SitemapGenerator;

class generateStieMapCasino extends Command
{
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

        $sitemapPath = public_path('sitemap.xml');

        $sitemap = SitemapGenerator::create(config('app.url'))
            ->getSitemap();

        // Ajoutez vos modèles Eloquent ici
        Casino::all()->each(function (Casino $casino) use ($sitemap) {
            $sitemap->add(config('app.url')."/".$casino->country_title."/".$casino->city_title."/".$casino->slug );
        });

        // Vérifiez si le fichier sitemap existe déjà et le remplacer si nécessaire
        if (file_exists($sitemapPath)) {
            unlink($sitemapPath);
        }

        // Enregistrez le sitemap dans un fichier public
        $sitemap->writeToFile($sitemapPath);





    }
}
