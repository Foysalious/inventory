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

            $table->string('name');
            $table->text('description')->nullable();
            $table->string('thumb')->default( getCollectionDefaultThumb() );
            $table->string('banner')->default( getCollectionDefaultBanner() );
            $table->string('app_thumb')->default( getCollectionDefaultAppThumb() );
            $table->string('app_banner')->default( getCollectionDefaultAppBanner() );
            $table->integer('is_published')->default(0);

            $table->timestamps();
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
