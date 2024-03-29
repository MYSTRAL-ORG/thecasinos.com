<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ClearLog extends Command
{
    protected $signature = 'log:clear';
    protected $description = 'Clear the application log';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        File::put(storage_path('logs/laravel.log'), '');
        $this->info('Logs have been cleared!');
    }
}
