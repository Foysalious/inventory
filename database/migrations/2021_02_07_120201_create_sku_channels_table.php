<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSkuChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sku_channels', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('sku_id')->nullable()->unsigned()->index();
            $table->foreign('sku_id')->references('id')->on('skus')
                ->onUpdate('cascade')->onDelete('set null');
            $table->bigInteger('channel_id')->nullable()->unsigned()->index();
            $table->foreign('channel_id')->references('id')->on('channels')
                ->onUpdate('cascade')->onDelete('set null');
            $table->decimal('price', 11, 2)->unsigned();
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
        Schema::dropIfExists('sku_channels');
    }
}
