<?php

namespace App\Console\Commands;
use App\services\ImagesService;
use Illuminate\Console\Command;


class ImageRenameCommande extends Command
{

    protected $signature = 'app:renameImage';

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
        $imagesService->renameImages();

    }
}
