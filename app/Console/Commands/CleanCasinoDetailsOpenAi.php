<?php

namespace App\Console\Commands;
use App\Models\Casino;
use App\Models\CasinoDetailsSource;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;


class CleanCasinoDetailsOpenAi extends Command
{



    protected $signature = 'app:cleanOpenAi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean Open AI Data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info("Starting : ".$this->description);
       // $this->extractedDataFromSite();
        $this->cleanData();
        Log::info("End  : ".$this->description);

    }

    private function cleanData()
    {
/*
        DB::delete("delete from  casino_details_source where  source_openai_json ->>'casino_sumup'  is null or
  source_openai_json ->>'casino_games'   is null or
 source_openai_json ->>'casino_fun_facts'   is null or
 source_openai_json ->>'casino_resume_1_line'   is null or
 source_openai_json ->>'casino_resume_2_words'   is null or
  source_openai_json ->>'novel'   is null or source_openai_json = '[]' or LENGTH(source_openai_json ->> 'novel')  <300");*/

        $query = " update casino_details_source set is_done = false ,new_desc = null where  new_desc is null or  array_length(regexp_split_to_array(trim(new_desc), E'\\W+'), 1) < 400  " ;
        DB::statement($query);


    }


}
