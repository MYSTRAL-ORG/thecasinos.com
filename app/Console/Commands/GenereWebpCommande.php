<?php

namespace App\Console\Commands;
use App\services\ImagesService;
use Illuminate\Console\Command;


class GenereWebpCommande extends Command
{

    protected $signature = 'app:webpImage';

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
        $imagesService->getnerateWebpImage();

    }
}
