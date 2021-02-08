<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Sheba\Product\SalesChannels;

class CreateProductChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_channels', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_id')->nullable()->unsigned();
            $table->foreign('product_id')->references('id')->on('products')
                ->onUpdate('cascade')->onDelete('set null');
            $table->bigInteger('channel_id')->nullable()->unsigned();
            $table->foreign('channel_id')->references('id')->on('channels')
                ->onUpdate('cascade')->onDelete('set null');
            $table->boolean('is_published')->default(1);
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
        Schema::dropIfExists('product_channels');
    }
}
