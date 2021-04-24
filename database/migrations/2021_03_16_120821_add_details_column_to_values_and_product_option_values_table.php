<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetailsColumnToValuesAndProductOptionValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('values', function (Blueprint $table) {
            $table->json('details')->nullable()->after('name');
        });
        Schema::table('product_option_values', function (Blueprint $table) {
            $table->json('details')->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('values', function (Blueprint $table) {
            $table->dropColumn('details');
        });
        Schema::table('product_option_values', function (Blueprint $table) {
            $table->dropColumn('details');
        });
    }
}
