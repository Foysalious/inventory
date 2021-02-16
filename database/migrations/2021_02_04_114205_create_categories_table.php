<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('parent_id')->nullable()->unsigned();
            $table->foreign('parent_id')->references('id')->on('categories')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->string('thumb')->default(getCategoryDefaultThumb())->nullable();
            $table->string('banner')->default(getCategoryDefaultBanner())->nullable();
            $table->string('app_thumb')->default(getCategoryDefaultThumb())->nullable();
            $table->string('app_banner')->default(getCategoryDefaultBanner())->nullable();
            $table->boolean('publication_status')->default(0);
            $table->tinyInteger('is_published_for_sheba')->default(1);
            $table->smallInteger('order')->nullable()->unsigned();
            $table->string('icon')->nullable();
            $table->string('icon_png')->nullable();
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
        Schema::dropIfExists('categories');
    }
}
