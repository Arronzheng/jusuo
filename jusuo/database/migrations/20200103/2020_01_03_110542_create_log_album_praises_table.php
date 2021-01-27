<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogAlbumPraisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_album_praises', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedTinyInteger('op_type')->comment('操作类型0取消点赞1点赞')->default(1);
            $table->unsignedInteger('album_id')->comment('方案id');
            $table->unsignedInteger('designer_id')->comment('操作着设计师id');
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
        Schema::dropIfExists('log_album_praises');
    }
}
