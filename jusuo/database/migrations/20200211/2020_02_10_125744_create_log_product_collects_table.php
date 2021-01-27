<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogProductCollectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_product_collects', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedTinyInteger('type')->comment('产品类型')->default(0);
            $table->unsignedInteger('product_id')->comment('产品id');
            $table->unsignedInteger('designer_id')->comment('设计师id');
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
        Schema::dropIfExists('log_product_collects');
    }
}
