<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewsBrandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news_brands', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('brand_id')->comment('品牌id');
            $table->string('web_id_code')->comment('显示id');
            $table->string('title')->comment('标题');
            $table->text('content')->comment('内容')->default('');
            $table->tinyInteger('status')->comment('状态，1启用0禁用')->default(1);
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
        Schema::dropIfExists('news_brands');
    }
}
