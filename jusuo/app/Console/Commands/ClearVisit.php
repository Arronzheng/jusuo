<?php

namespace App\Console\Commands;

use App\Services\v1\site\OpService;
use Illuminate\Console\Command;

class ClearVisit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ClearVisit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $stat = new OpService();
        $stat->clearVisit();
    }
}
