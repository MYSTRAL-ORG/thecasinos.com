<?php

namespace App\Console\Commands;
use App\Models\Casino;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;
use Illuminate\Console\Command;
use Symfony\Component\DomCrawler\Crawler;


class CasinoCrawler extends Command
{
//https://api.proxyscrape.com/v2/account/datacenter_shared/proxy-list?auth=tsi8nqv9ph8f94ydgtgh&type=getproxies&country[]=all&protocol=http&format=normal&status=all

    public static string $domainName = "https://www.worldcasinodirectory.com";
    public static string $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.0.0 Safari/537.36';

    public static string $proxy = 'https://api.proxyscrape.com/v2/account/datacenter_shared/proxy-list?auth=tsi8nqv9ph8f94ydgtgh&type=getproxies&country[]=all&protocol=http&format=normal&status=all';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:casino-crawler';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

       // $this->extractedDataFromSite();
        $this->extractedImageFromSite();

    }
    function getRandomDelay($minSeconds, $maxSeconds)
    {
        return mt_rand($minSeconds, $maxSeconds);
    }
    function extractValue(Crawler $crawler, $xpath) {
        try {
            // Récupérez la valeur en utilisant l'expression XPath fournie
            $c = $crawler->filterXPath($xpath);
            return ($c->count() > 0 ) ?$c : null;
        } catch (\Exception $e) {
           Log::info($e->getMessage());
            return null;
        }
    }


    public function scrapImage($url , $image_name){
        $imageData = file_get_contents($url);
        $destinationPath = storage_path('/app/public/images/casino/');
        file_put_contents($destinationPath.$image_name , $imageData);
    }

    /**
     * @return void
     */
    public function extractedDataFromSite(): void
    {
        $casinos = Casino::all()->where('is_scrap', false)->take(40);
        foreach ($casinos as $casino) {


            $url = $this::$domainName . '/casino/' . $casino->slug;
            log::info($url);

            // Créez une instance du client HTTP avec le proxy
            $httpClient = HttpClient::create([
                // 'proxy' => $this::$proxy,
                'headers' => [
                    'User-Agent' => $this::$userAgent,
                ],
            ]);

            // Créez une instance du navigateur avec le client HTTP
            $browser = new HttpBrowser($httpClient);

            // Accédez à l'URL avec le navigateur
            $crawler = $browser->request('GET', $url);
            /*
                    // Récupérez et traitez les données
                    $content = $crawler->html();
                    $crawler.setsour
                    $destinationPath = storage_path('/app/public/images/');

                    file_put_contents($destinationPath."testCommande.html" , $content);
            */
            // $crawler = $client->request('GET', $url);
            //   $contents = Storage::get("/public/images/testCommande.html");
            //  $crawler = $browser->createCrawlerFromContent($url, $contents, "text/html; charset=UTF-8");
            // Utilisez un sélecteur CSS ou XPath pour cibler la valeur "140,000 sq/ft"
            $areaValue = null;


            $crawlerTollFree = $this->extractValue($crawler, '//strong[contains(text(), "Toll-free")]');
            $areaValue["Toll"] = ($crawlerTollFree == null) ? null : $crawlerTollFree->nextAll()->text();


            $crawlerAdresse = $this->extractValue($crawler, '//span[@itemprop="address"]');
            $areaValue["adresse"] = ($crawlerAdresse == null) ? null : $crawlerAdresse->text();

            $crawlerTelephone = $this->extractValue($crawler, '//span[@itemprop="telephone"]');
            $areaValue["telephone"] = ($crawlerTelephone == null) ? null : $crawlerTelephone->text();


            $crawlerWebsite = $this->extractValue($crawler, '//strong[contains(text(), "Website")]');
            $areaValue["website"] = ($crawlerWebsite == null) ? null : $crawlerWebsite->nextAll()->attr('href');

            $crawlerEmail = $this->extractValue($crawler, '//strong[contains(text(), "Email")]');
            $areaValue["email"] = ($crawlerEmail == null) ? null : $crawlerEmail->nextAll()->attr('href');

            $crawlerFacebook = $this->extractValue($crawler, '//strong[contains(text(), "Facebook ")]');
            $areaValue["facebook"] = ($crawlerFacebook == null) ? null : $crawlerFacebook->nextAll()->attr('href');

            $crawlerTwitter = $this->extractValue($crawler, '//strong[contains(text(), "Twitter ")]');
            $areaValue["twitter"] = ($crawlerTwitter == null) ? null : $crawlerTwitter->nextAll()->attr('href');

            /* if($casino->img_url != null){


               try {

                   $areaValue["img"] = $this::$domainName.'/assets/images/pop_images/height/600/casinos/'.str_replace(' ', '%20', $casino->img_url);
                   $this->scrapImage($areaValue["img"] , $casino->id.".jpg" );
               } catch (\Exception $e) {
                   Log::info($e->getMessage());
                   return null;
               }
             }*/


            // $areaValue["adresse"] = $crawler->filter('.mapboxgl-popup-close-button')->text();
            // $this->scrapImage(str_replace(' ', '%20', $this::$domainName.$areaValue["img"]) , "test.jpg");

            Log::info($areaValue);
            $casino->is_scrap = true;
            $casino->address = $areaValue["adresse"];
            $casino->telephone = $areaValue["telephone"];
            $casino->website = $areaValue["website"];
            $casino->facebook = $areaValue["facebook"];
            $casino->twitter = $areaValue["twitter"];
            $casino->toll_free = $areaValue["Toll"];
            $casino->save();
            // Appliquer un délai aléatoire entre les requêtes
            $minDelay = 1; // Délai minimum en secondes
            $maxDelay = 10; // Délai maximum en secondes

            sleep($this->getRandomDelay($minDelay, $maxDelay));
        }
    }
    public function extractedImageFromSite(): void
    {
        $casinos = Casino::whereNotNull('img_url')->get();
        foreach ($casinos as $casino) {

            if(!file_exists(storage_path('/app/public/images/casino/'.$casino->id.".jpg"))){


                $url = $this::$domainName.'/assets/images/pop_images/height/600/casinos/'.str_replace(' ', '%20', $casino->img_url);
                Log::info($url);
                $this->scrapImage($url , $casino->id.".jpg" );
                /*$minDelay = 1; // Délai minimum en secondes
                $maxDelay = 10; // Délai maximum en secondes

                sleep($this->getRandomDelay($minDelay, $maxDelay));*/
            }

            /* if($casino->img_url != null){


               /*try {

                   $areaValue["img"] = $this::$domainName.'/assets/images/pop_images/height/600/casinos/'.str_replace(' ', '%20', $casino->img_url);
                   $this->scrapImage($areaValue["img"] , $casino->id.".jpg" );
               } catch (\Exception $e) {
                   Log::info($e->getMessage());
                   return ;
               }
             }*/


            // Appliquer un délai aléatoire entre les requêtes
           /* $minDelay = 1; // Délai minimum en secondes
            $maxDelay = 10; // Délai maximum en secondes

            sleep($this->getRandomDelay($minDelay, $maxDelay));*/
        }
    }
}
