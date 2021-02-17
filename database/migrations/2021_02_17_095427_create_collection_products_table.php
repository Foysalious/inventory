<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollectionProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collection_products', function (Blueprint $table) {

            $table->id();

            $table->bigInteger('collection_id')->nullable()->unsigned()->index();
            $table->foreign('collection_id')->references('id')->on('collections')
                ->onUpdate('cascade')->onDelete('set null');

            $table->bigInteger('product_id')->nullable()->unsigned()->index();
            $table->foreign('product_id')->references('id')->on('products')
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
        Schema::dropIfExists('collection_products');
    }
}
