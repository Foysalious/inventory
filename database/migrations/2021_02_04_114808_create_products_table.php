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
            $table->bigInteger('category_id')->unsigned();
            $table->foreign('category_id')->references('id')->on('categories')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->string('name');
            $table->tinyInteger('is_published')->default(1);
            $table->tinyInteger('is_published_for_shop')->default(0);
            $table->text('description');
            $table->string('shape');
            $table->tinyInteger('show_image');
            $table->tinyInteger('warranty');
            $table->decimal('vat_percentage', 5, 2)->default(0);
            $table->enum('portal_name', config('sheba.portals'))->nullable();
            $table->ipAddress('ip')->nullable();
            $table->string('user_agent')->nullable();
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
