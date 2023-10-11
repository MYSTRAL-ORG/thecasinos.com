<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Models\SourceCasino;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Goutte\Client;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\DomCrawler\Crawler;

class CrawlerController extends Controller
{
    public function scrapeTableGames()
    {

        $domain = "https://www.worldcasinodirectory.com";
        $url = $domain.'/casino/bellagio-2534';
        $client = new Client();

        // $crawler = $client->request('GET', $url);
        $contents = Storage::get("/public/file.txt");
        $crawler = $client->createCrawlerFromContent($url, $contents, "text/html; charset=UTF-8");
        // Utilisez un sélecteur CSS ou XPath pour cibler la valeur "140,000 sq/ft"
        $areaValue = null;



        $areaValue["Toll-free"] = $crawler->filterXPath('//strong[contains(text(), "Toll-free")]')->nextAll()->text();

        $areaValue["adresse"] = $crawler->filterXPath('//span[@itemprop="address"]')->text();
        $areaValue["telephone"] = $crawler->filterXPath('//span[@itemprop="telephone"]')->text();
        $areaValue["website"] = $crawler->filterXPath('//strong[contains(text(), "Website")]')->nextAll()->attr('href');
        $areaValue["email"] = $crawler->filterXPath('//strong[contains(text(), "Email")]')->nextAll()->attr('href');
        $areaValue["facebook"] = $crawler->filterXPath('//strong[contains(text(), "Facebook ")]')->nextAll()->attr('href');
       // $areaValue["twitter"] = $crawler->filterXPath('//strong[contains(text(), "Twitter ")]')->nextAll()->attr('href');

        $areaValue["img"] = $crawler->filter('.bonusesTitle')->nextAll()->attr('src');

        // $areaValue["adresse"] = $crawler->filter('.mapboxgl-popup-close-button')->text();
        $this->scrapImage(str_replace(' ', '%20', $domain.$areaValue["img"]) , "test.jpg");
        dd($areaValue);


        return $areaValue;
    }

    private function getXpathDataSpan(Crawler $crawler, array $listData, $areaValue)
    {
        foreach ($listData as $data) {
            //var_dump($data);
            Log::info($data);;
            $areaValue[$data] = $crawler->filterXPath('//span[contains(text(), "' . $data . '")]')->nextAll()->text();
            Log::info($areaValue[$data]);;
        }
        return $areaValue;
    }

    private function getXpathDataStrong(Crawler $crawler, array $listData, $areaValue)
    {
        foreach ($listData as $data) {
            //var_dump($data);
            Log::info($data);;
            $areaValue[$data] = $crawler->filterXPath('//strong[contains(text(), "' . $data . '")]')->nextAll()->text();
            Log::info($areaValue[$data]);;
        }
        return $areaValue;
    }


    public function parseJson()
    {
        //$jsonFilePath = 'path/to/your/json/file.json';
        $jsonData = Storage::json("/public/listeDesCasinos.json", false );
        $test = array();
        foreach ($jsonData['items'] as $item) {

            //dd(json_encode($item)); dd(json_encode($item));
                SourceCasino::create([
                'created_id' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'data' => json_encode($item),
                // Add other fields as needed
            ]);

        }

        return 'Data from JSON file has been saved to the database.';
    }


    public function scrapImage($url , $image_name){


        $imageData = file_get_contents($url);

        $destinationPath = storage_path('/app/public/images/');

        file_put_contents($destinationPath.$image_name , $imageData);
    }


}



