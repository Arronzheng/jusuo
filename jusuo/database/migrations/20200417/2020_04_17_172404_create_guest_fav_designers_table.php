<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGuestFavDesignersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guest_fav_designers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('target_designer_id')->comment('设计师id');
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
        Schema::dropIfExists('guest_fav_designers');
    }
}
