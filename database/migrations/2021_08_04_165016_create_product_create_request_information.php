<?php

use App\Services\Product\Constants\PortalNames;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductCreateRequestInformation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_requests', function (Blueprint $table) {
            $table->id();
            $table->string('route')->nullable();
            $table->enum('portal', PortalNames::get())->nullable();
            $table->string('portal_version')->nullable();
            $table->string('ip')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });
        Schema::table('products', function (Blueprint $table) {
            $table->bigInteger('api_request_id')->after('unit_id')->nullable()->unsigned();
            $table->foreign('api_request_id')->references('id')->on('api_requests')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('api_request_id');
        });
        Schema::dropIfExists('api_requests');
    }
}
