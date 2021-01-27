<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogAlbumDownloadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_album_downloads', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedTinyInteger('op_type')->comment('操作类型0下载1复制');
            $table->unsignedInteger('album_id')->comment('方案id');
            $table->unsignedTinyInteger('designer_id')->comment('下载/复制者设计师id');
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
        Schema::dropIfExists('log_album_downloads');
    }
}
