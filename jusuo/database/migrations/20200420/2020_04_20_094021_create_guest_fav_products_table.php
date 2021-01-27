<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGuestFavProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guest_fav_products', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_id')->comment('产品id');
            $table->unsignedInteger('guest_id')->comment('游客id');
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
        Schema::dropIfExists('guest_fav_products');
    }
}
