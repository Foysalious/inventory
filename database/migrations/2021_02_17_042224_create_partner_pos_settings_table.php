<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartnerPosSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_pos_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('partner_id')->unsigned();
            $table->bigInteger('sharding_id')->nullable()->unsigned()->index();
            $table->foreign('partner_id')->references('id')->on('partners')->onUpdate('cascade');
            $table->decimal('vat_percentage', 5, 2)->default(0);
            $table->string('printer_model')->nullable();
            $table->string('printer_name')->nullable();
            $table->tinyInteger('auto_printing');
            $table->tinyInteger('sms_invoice');
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
        Schema::dropIfExists('partner_pos_settings');
    }
}
