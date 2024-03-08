<?php

namespace App\Console\Commands;
use App\Models\Casino;
use App\services\ImagesService;
use App\services\OpenAiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;


class OpenAiCommande extends Command
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
    protected $signature = 'app:openai';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(OpenAiService $openAiService){
        $openAiService->storeResponseFromChatGPT();
    }
}
