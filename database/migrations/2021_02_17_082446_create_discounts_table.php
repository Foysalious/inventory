<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Services\Discount\Types;

class CreateDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('type_id');
            $table->enum('discount_type',Types::get())->index();
            $table->decimal('amount');
            $table->tinyInteger('is_amount_percentage');
            $table->decimal('cap');
            $table->timestamp('start_date');
            $table->timestamp('end_date');
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
        Schema::dropIfExists('discounts');
    }
}
