<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSearchProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_products', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_id')->comment('产品id');
            $table->string('name',200)->comment('产品名称');
            $table->string('code',200)->comment('产品编号');
            $table->string('product_category',100)->comment('经营类别ids');
            $table->string('brand')->comment('品牌ids');
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
        Schema::dropIfExists('search_products');
    }
}
