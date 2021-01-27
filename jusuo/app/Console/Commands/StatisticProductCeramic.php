<?php

namespace App\Console\Commands;

use App\Services\v1\admin\StatisticAccountBrandService;
use App\Services\v1\admin\StatisticAccountDealerService;
use App\Services\v1\admin\StatisticAlbumService;
use App\Services\v1\admin\StatisticProductCeramicService;
use Illuminate\Console\Command;

class StatisticProductCeramic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'StatisticProductCeramic';

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
        $stat = new StatisticProductCeramicService();
        $stat->update();
    }
}
