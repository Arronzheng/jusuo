<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGuestLikeAlbumsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guest_like_albums', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('album_id')->comment('方案id');
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
        Schema::dropIfExists('guest_like_albums');
    }
}
