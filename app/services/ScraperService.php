<?php

namespace App\services;

use App\Models\SourceCasino;
use Carbon\Carbon;
use Goutte\Client;
use Illuminate\Support\Facades\Log;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
class ScraperService
{
    public function scrapeTableGames()
    {

       /* $domain = "https://www.worldcasinodirectory.com";
        $url = $domain.'/casino/bellagio-2534';
        $client = new Client();


        // Entête User-Agent pour simuler Chrome
        $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.0.0 Safari/537.36';

        // Configuration du proxy
        $proxy = 'http://adresse_du_proxy:port_du_proxy';

        // Créez une instance du client Guzzle avec le proxy
        $guzzle = HttpClient::create([
            'headers' => [
                'User-Agent' => $userAgent,
            ],
            'proxy' => $proxy,
        ]);

        // Associez le client Guzzle à Goutte
        $client->setClient($guzzle);

        // Accédez à l'URL avec l'entête User-Agent et le proxy
        $crawler = $client->request('GET', $url, [], [], ['HTTP_USER_AGENT' => $userAgent]);

*/
    // Créez une instance du client Goutte
        $client = new Client();

    // URL du site que vous souhaitez scraper
        $url = 'https://www.example.com';

    // Entête User-Agent pour simuler Chrome
        $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.0.0 Safari/537.36';

    // Configuration du proxy
        $proxy = 'http://adresse_du_proxy:port_du_proxy';

    // Créez une instance du client HTTPBrowser avec le proxy
        $httpClient = HttpClient::create(['proxy' => $proxy]);
        $browser = new HttpBrowser($httpClient);

    // Configurez le client Goutte pour utiliser le navigateur personnalisé
        $client->setClient($browser);

    // Définissez l'entête User-Agent
        $client->setServerParameter('HTTP_USER_AGENT', $userAgent);

    // Accédez à l'URL
        $crawler = $client->request('GET', $url);

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
