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
            $table->bigInteger('partner_id')->nullable()->unsigned()->index();
            $table->foreign('partner_id')->references('id')->on('partners')
                ->onUpdate('cascade')->onDelete('set null');
            $table->bigInteger('category_id')->nullable()->unsigned()->index();
            $table->foreign('category_id')->references('id')->on('categories')
                ->onUpdate('cascade')->onDelete('set null');
            $table->string('name');
            $table->text('description')->nullable();
            $table->tinyInteger('show_image')->default(1);
            $table->integer('warranty')->default(0);
            $table->enum('warranty_unit', array_keys(config('pos.warranty_unit')))->default('day');
            $table->decimal('vat_percentage', 5, 2)->default(0);
            $table->bigInteger('unit_id')->unsigned()->nullable();
            $table->foreign('unit_id')->references('id')->on('units')
                ->onUpdate('cascade')->onDelete('set null');
            $table->enum('portal_name', config('sheba.portals'))->nullable()->index();
            $table->ipAddress('ip')->nullable()->index();
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
