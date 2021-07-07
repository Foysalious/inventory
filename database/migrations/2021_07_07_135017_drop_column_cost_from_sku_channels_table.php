<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumnCostFromSkuChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sku_channels', function (Blueprint $table) {
            $table->dropColumn('cost');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sku_channels', function (Blueprint $table) {
            $table->decimal('cost', 11, 2)->unsigned()->after('channel_id');
        });
    }
}
