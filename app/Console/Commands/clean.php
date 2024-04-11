<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class clean extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean all cache';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {


        $this->call('key:generate');
        $this->call('route:cache');
        $this->call('cache:clear');
        $this->call('view:clear');
        $this->call('route:clear');
        $this->call('config:clear');
        $this->call('view:clear');
        $this->call('clear-compiled');
        $this->call('config:cache');
        $this->call('optimize:clear');


    }
}
