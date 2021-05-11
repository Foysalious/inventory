<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnIsPublishedAndIsDefaultInCategoryPartnerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('category_partner', function (Blueprint $table) {
            $table->tinyInteger('is_default')->default(0)->after('category_id');
            $table->tinyInteger('is_published')->default(0)->after('is_default');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('category_partner', function (Blueprint $table) {
            $table->dropColumn('is_default');
            $table->dropColumn('is_published');
        });
    }
}
