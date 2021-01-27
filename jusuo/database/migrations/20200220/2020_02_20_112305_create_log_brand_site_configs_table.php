<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogBrandSiteConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_brand_site_configs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('target_brand_id')->comment('品牌id');
            $table->string('content',2000)->comment('设置信息');
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
        Schema::dropIfExists('log_brand_site_configs');
    }
}
