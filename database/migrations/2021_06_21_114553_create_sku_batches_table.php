<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSkuBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sku_batches', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('sku_id')->nullable()->unsigned()->index();
            $table->foreign('sku_id')->references('id')->on('skus')
                ->onUpdate('cascade')->onDelete('set null');
            $table->decimal('stock', 11, 2)->nullable()->index();
            $table->decimal('cost', 11, 2)->unsigned()->default(0.0);
            $table->softDeletes();
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
        Schema::dropIfExists('sku_batches');
    }
}
