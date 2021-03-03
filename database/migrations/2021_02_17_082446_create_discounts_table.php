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
            $table->increments('id')->startingValue(config('migration.starting_ids.discounts'));
            $table->integer('type_id')->index();
            $table->enum('type',Types::get())->index();
            $table->decimal('amount');
            $table->tinyInteger('is_amount_percentage')->default(0);
            $table->decimal('cap')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
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
