<?php

namespace App\Console\Commands;
use App\services\GenerateJsonService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class createJsonCasino extends Command
{



    protected $signature = 'app:json';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Json File';

    /**
     * Execute the console command.
     */
    public function handle(GenerateJsonService $generateJsonService)
    {
        Log::info("Starting : ".$this->description);
       // $this->extractedDataFromSite();
        $generateJsonService->writeJson();
        Log::info("End  : ".$this->description);

    }



}
