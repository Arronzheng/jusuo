<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogAlbumTopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_album_tops', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedTinyInteger('op_type')->comment('操作类型0取消1置顶');
            $table->unsignedInteger('album_id')->comment('方案id');
            $table->unsignedTinyInteger('organization_type')->comment('组织置顶类型0平台1品牌2销售商3设计师');
            $table->unsignedInteger('operator_id')->comment('操作者id');
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
        Schema::dropIfExists('log_album_tops');
    }
}
