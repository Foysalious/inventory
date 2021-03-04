<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collections', function (Blueprint $table) {

            $table->id();

            $table->bigInteger('sharding_id')->nullable()->unsigned()->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('thumb')->default( getCollectionDefaultThumb() )->nullable();
            $table->string('banner')->default( getCollectionDefaultBanner() )->nullable();
            $table->string('app_thumb')->default( getCollectionDefaultAppThumb() )->nullable();
            $table->string('app_banner')->default( getCollectionDefaultAppBanner() )->nullable();
            $table->integer('is_published')->default(0)->unsigned()->index();

            // Foreign key relationship with Partner
            $table->bigInteger('partner_id')->nullable()->unsigned()->index();
            $table->foreign('partner_id')->references('id')->on('partners')
                ->onUpdate('cascade')->onDelete('set null');

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
        Schema::dropIfExists('collections');
    }
}
