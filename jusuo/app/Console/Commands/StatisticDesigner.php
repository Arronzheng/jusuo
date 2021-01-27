<?php

namespace App\Console\Commands;

use App\Services\v1\admin\StatisticAccountBrandService;
use App\Services\v1\admin\StatisticAccountDealerService;
use App\Services\v1\admin\StatisticDesignerService;
use Illuminate\Console\Command;

class StatisticDesigner extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'StatisticDesigner';

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
        $service = new StatisticDesignerService();
        $service->update();
    }
}
