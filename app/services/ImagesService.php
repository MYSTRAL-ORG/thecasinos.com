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

            //cut $casino->img_url in two strings separated by the string "."
            $arrayImg = explode(".", $casino->img_url);


          //get $arrayImg length
                $length = count($arrayImg);

                $storage_image_path = public_path('/img/casino/' . $casino->img_url  );

                $newFile = '/img/casino/' . str_replace(" ","_",$casino->slug) . "." . $arrayImg[$length-1];
                $new_storage_image_path = public_path(  $newFile);


                if (File::exists($storage_image_path)) {

                    File::move($storage_image_path, $new_storage_image_path);

                }
                $casino->img_url = $newFile;
                $casino->save();


        }


    }


    public function getnerateFakeLinkImage(){


        //update model Casino set img_url from random array string a never pic the same string until the end of the array
        $casinos =  Casino::whereNull('img_url')->get();
        $directory = public_path('/img/casinos/randomCasinos');
        $files = File::allFiles($directory);
        $fileNamesRadomCasinos = collect($files)->map(function ($file) {
            return $file->getFilename();
        });

        $randomStringsCount = count($fileNamesRadomCasinos);
        $index = 0;

        foreach ($casinos as $casino) {


                $casino->img_url = $fileNamesRadomCasinos[$index];
                $index = ($index + 1) % $randomStringsCount;

            $casino->save();





        }

    }


}
