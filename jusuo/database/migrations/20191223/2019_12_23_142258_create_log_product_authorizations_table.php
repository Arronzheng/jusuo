<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogProductAuthorizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_product_authorizations', function (Blueprint $table) {
            $table->increments('id');
            $table->text('brand_id')->comment('品牌id（冗余）');
            $table->text('administrator_id')->comment('管理员id');
            $table->unsignedInteger('product_type')->comment('产品类型（0瓷砖）');
            $table->unsignedInteger('log_type')->comment('0显示授权1结构授权2价格授权');
            $table->unsignedInteger('log_type_param')->comment('授权类型参数（比如授权什么结构、什么价格）');
            $table->text('product_ids')->comment('产品ids');
            $table->text('object_ids')->comment('对象id（默认销售商id）');
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
        Schema::dropIfExists('log_product_authorizations');
    }
}
