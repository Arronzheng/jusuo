<?php

namespace App\Console;

use App\Console\Commands\StatisticAccountDealer;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //产品
        Commands\LogProductSaleArea::class,
        Commands\ProductColumnStatistic::class,
        Commands\StatisticProductCeramic::class,
        //方案
        Commands\StatisticAlbum::class,
        Commands\AlbumColumnStatistic::class,
        //设计师
        Commands\StatisticDesigner::class,
        Commands\DesignerColumnStatistic::class,
        //销售商
        Commands\StatisticAccountDealer::class,
        Commands\OrganizationDealerColumnStatistic::class,
        //品牌
        Commands\StatisticAccountBrand::class,
        Commands\OrganizationBrandColumnStatistic::class,
        //其他
        Commands\ClearVisit::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //先统计产品、方案等信息，再统计设计师、组织的信息，有先后顺序
        //产品
        $schedule->command('LogProductSaleArea')
            ->dailyAt('00:01');
        $schedule->command('ProductColumnStatistic')
            ->dailyAt('00:03');
        $schedule->command('StatisticProductCeramic')
            ->dailyAt('00:05');


        //方案
        $schedule->command('StatisticAlbum')
            ->dailyAt('00:15');
        $schedule->command('AlbumColumnStatistic')
            ->dailyAt('00:25');

        //设计师
        $schedule->command('StatisticDesigner')
            ->dailyAt('00:30');
        $schedule->command('DesignerColumnStatistic')
            ->dailyAt('00:40');

        //销售商
        $schedule->command('StatisticAccountDealer')
            ->dailyAt('01:00');
        $schedule->command('OrganizationDealerColumnStatistic')
            ->dailyAt('01:10');

        //品牌
        $schedule->command('StatisticAccountBrand')
            ->dailyAt('01:20');
        $schedule->command('OrganizationBrandColumnStatistic')
            ->dailyAt('01:30');

        //方案、设计师、产品、销售商30天前的浏览记录
        $schedule->command('ClearVisit')
            ->dailyAt('06:00');

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
