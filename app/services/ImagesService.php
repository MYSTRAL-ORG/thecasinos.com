<?php

namespace App\services;

use App\Models\Casino;
use App\Models\SourceCasino;
use Carbon\Carbon;
use Goutte\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;
use Illuminate\Support\Facades\File;
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

    public function renameImages()
    {


        $records  = Casino::whereNotNull('img_url')->get();

        foreach ($records as $casino) {
            Log::info('fdsqffdsfqfsd');
            $storage_image_path = public_path('/img/casino/' . $casino->id . ".jpg");

            $new_storage_image_path = public_path('/img/casino/' . $casino->img_url);


            if (File::exists($storage_image_path)) {

                File::move($storage_image_path, $new_storage_image_path);

            }

        }


    }

}
