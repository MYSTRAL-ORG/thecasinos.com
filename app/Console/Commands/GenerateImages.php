<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Intervention\Image\Facades\Image;
use File;
use Log;

class GenerateImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resize images and move to respective directories based on device type';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sourceDir = public_path('img/casino'); // The source directory
        $sizes = [
            'mobile' => 300,   // Width for mobile devices
        ];

        if (!File::isDirectory($sourceDir)) {
            $this->error("The source directory does not exist.");
            return 1;
        }

        foreach (File::files($sourceDir) as $file) {
            $image = Image::make($file->getPathname());

            foreach ($sizes as $folder => $width) {
                $targetDirPath = public_path('img/casino/' . $folder);

                File::isDirectory($targetDirPath) or File::makeDirectory($targetDirPath, 0755, true);

                $resizedImage = $image->resize($width, null, function ($constraint) {
                    $constraint->aspectRatio();
                });

                $resizedImage->save($targetDirPath . '/' . $file->getFilename());
                $this->info("Saved resized image to {$targetDirPath}/{$file->getFilename()}");
            }
        }

        //$this->info("Images have been resized and moved to respective folders.");
        return 0;
    }
}
