<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('partner_id')->unsigned();
            $table->bigInteger('category_id')->unsigned();
            $table->foreign('category_id')->references('id')->on('categories')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->string('name');
            $table->boolean('is_published')->default(1);
            $table->boolean('is_published_for_online')->default(0);
            $table->text('description')->nullable();
            $table->string('color')->nullable();
            $table->string('shape')->nullable();
            $table->tinyInteger('show_image')->default(1);
            $table->integer('warranty')->default(0);
            $table->enum('warranty_unit', array_keys(config('pos.warranty_unit')))->default('day');
            $table->decimal('vat_percentage', 5, 2)->default(0);
            $table->enum('portal_name', config('sheba.portals'))->nullable();
            $table->ipAddress('ip')->nullable();
            $table->string('user_agent')->nullable();
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
        Schema::dropIfExists('products');
    }
}
