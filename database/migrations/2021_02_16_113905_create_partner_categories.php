<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartnerCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_partner', function (Blueprint $table) {
            $table->id()->startingValue(config('migration.starting_ids.category_partner'));
            $table->bigInteger('partner_id')->nullable()->unsigned();
            $table->bigInteger('sharding_id')->unsigned()->nullable()->index();
            $table->foreign('partner_id')->references('id')->on('partners')
                ->onUpdate('cascade')->onDelete('set null');
            $table->bigInteger('category_id')->nullable()->unsigned();
            $table->foreign('category_id')->references('id')->on('categories')
                ->onUpdate('cascade')->onDelete('set null');
            $table->unique(['partner_id', 'category_id']);
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
        Schema::dropIfExists('category_partner');
    }
}
