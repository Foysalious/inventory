<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCombinationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('combinations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('sku_id')->nullable()->unsigned();
            $table->foreign('sku_id')->references('id')->on('skus')
                ->onUpdate('cascade')->onDelete('set null');
            $table->bigInteger('product_option_value_id')->nullable()->unsigned();
            $table->foreign('product_option_value_id')->references('id')->on('product_option_values')
                ->onUpdate('cascade')->onDelete('set null');
            commonColumns($table);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('combinations');
    }
}
