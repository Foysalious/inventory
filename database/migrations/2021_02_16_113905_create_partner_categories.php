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
        Schema::create('partner_categories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('partner_id')->unsigned();
            $table->bigInteger('sharding_id')->unsigned()->nullable()->index();
            $table->foreign('partner_id')->references('id')->on('partners')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->bigInteger('category_id')->unsigned();
            $table->foreign('category_id')->references('id')->on('categories')
                ->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('partner_categories');
    }
}
