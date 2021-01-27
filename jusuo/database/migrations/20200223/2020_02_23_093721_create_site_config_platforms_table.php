<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteConfigPlatformsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_config_platforms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('config_name')->comment('设置名称');
            $table->string('value',2000)->comment('设置值');
            $table->string('type')->comment('设置类型');
            $table->string('notice')->comment('设置值');
            $table->string('var_name')->comment('变量名');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('site_config_platforms');
    }
}
