<?php

namespace App\Console\Commands;

use App\Services\v1\admin\OrganizationBrandColumnStatisticService;
use App\Services\v1\admin\StatisticAccountBrandService;
use Illuminate\Console\Command;

class OrganizationBrandColumnStatistic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'OrganizationBrandColumnStatistic';

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
        $stat = new OrganizationBrandColumnStatisticService();
        $stat->update();
    }
}
