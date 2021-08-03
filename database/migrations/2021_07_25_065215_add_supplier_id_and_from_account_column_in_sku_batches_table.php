<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSupplierIdAndFromAccountColumnInSkuBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sku_batches', function (Blueprint $table) {
            $table->integer('supplier_id')->after('sku_id')->nullable();
            $table->string('from_account')->after('supplier_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sku_batches', function (Blueprint $table) {
            $table->dropColumn('supplier_id');
            $table->dropColumn('from_account');
        });
    }
}
