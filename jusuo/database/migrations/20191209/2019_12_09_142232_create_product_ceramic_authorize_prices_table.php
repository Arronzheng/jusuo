<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductCeramicAuthorizePricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_ceramic_authorize_prices', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('authorization_id')->comment('瓷砖产品授权销售商关系id');
            $table->unsignedTinyInteger('type')->comment('定价方式（1统一定价/2浮动定价/3渠道定价/4不定价）');
            $table->double('price',10,2)->comment('价格')->nullable();
            $table->unsignedTinyInteger('unit')->comment('单位')->nullable();
            $table->double('float',10,2)->comment('上下浮动(浮动定价专用)')->nullable();
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
        Schema::dropIfExists('product_ceramic_authorize_prices');
    }
}
