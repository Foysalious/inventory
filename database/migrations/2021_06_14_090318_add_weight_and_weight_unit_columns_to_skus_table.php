<?php

use App\Services\Sku\WeightUnit;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWeightAndWeightUnitColumnsToSkusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('skus', function (Blueprint $table) {
            $table->decimal('weight')->nullable()->after('stock');
            $table->enum('weight_unit', WeightUnit::get())->nullable()->after('weight');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('skus', function (Blueprint $table) {
            $table->dropColumn('weight');
            $table->dropColumn('weight_unit');
        });
    }
}
