<?php

namespace App\Console\Commands;
use App\Models\Casino;
use App\services\ImagesService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;


class ImageCommande extends Command
{

    protected $signature = 'app:compressImage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compression image';

    /**
     * Execute the console command.
     */
    public function handle(ImagesService $imagesService)
    {
        $imagesService->compressImages();

    }
}
