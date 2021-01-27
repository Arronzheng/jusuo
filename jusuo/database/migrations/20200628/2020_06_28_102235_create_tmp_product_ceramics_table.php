<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTmpProductCeramicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tmp_product_ceramics', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('brand_id')->comment('品牌id');
            $table->unsignedInteger('target_product_id')->comment('目标产品id');
            $table->text('content')->comment('待审核信息的serialize字符串（数组，在字段值前加上字段名）');
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
        Schema::dropIfExists('tmp_product_ceramics');
    }
}
