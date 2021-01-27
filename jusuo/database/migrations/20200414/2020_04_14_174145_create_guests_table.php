<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGuestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guests', function (Blueprint $table) {
            $table->increments('id');
            $table->string('login_wx_openid',32)->comment('微信openid');
            $table->timestamp('last_active_time')->comment('最后活跃时间');
            $table->string('remember_token',100)->comment('');
            $table->string('web_id_code',32)->comment('随机id码（网站访问用）');
            $table->string('status',5)->comment('状态（000待审核，200正常，100禁用）');
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
        Schema::dropIfExists('guests');
    }
}
