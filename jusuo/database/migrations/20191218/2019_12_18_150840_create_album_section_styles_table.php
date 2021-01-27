<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlbumSectionStylesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('album_section_styles', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('album_id')->comment('方案id（冗余）');
            $table->unsignedInteger('album_section_id')->comment('方案章节id');
            $table->unsignedInteger('style_id')->comment('风格id');
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
        Schema::dropIfExists('album_section_styles');
    }
}
