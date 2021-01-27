<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGuestFavDealersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guest_fav_dealers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('target_dealer_id')->comment('销售商id');
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
        Schema::dropIfExists('guest_fav_dealers');
    }
}
