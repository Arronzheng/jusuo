<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductCeramicAuthorizeStructuresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_ceramic_authorize_structures', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('authorization_id')->comment('瓷砖授权销售商关系id');
            $table->unsignedInteger('structure_id')->comment('瓷砖产品结构id');
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
        Schema::dropIfExists('product_ceramic_authorize_structures');
    }
}
