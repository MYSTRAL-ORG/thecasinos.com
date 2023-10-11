<?php

namespace App\services;

use App\Models\SourceCasino;
use Carbon\Carbon;
use Goutte\Client;
use Illuminate\Support\Facades\Log;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;
use Intervention\Image\Facades\Image;
class ImagesService
{

    public function compressImages()
    {
        // Get all images from a directory (e.g., public/images)
        $images = glob(public_path('img/casino') . '/*.jpg');

        foreach ($images as $imagePath) {
            // Open the image using Intervention Image
            $img = Image::make($imagePath);

            // Resize the image to 320x320 pixels (maintaining the aspect ratio)
            $img->resize(320, 320, function ($constraint) {
                $constraint->aspectRatio();
            });

            // Save the compressed image
            $img->save($imagePath);
        }

        return 'Images compressed successfully!';
    }

}
