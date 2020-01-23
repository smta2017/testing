<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('customer_id');
            $table->bigInteger('address_type_id');
            $table->bigInteger('location_id');
            $table->float('latitude');
            $table->float('longitude');
            $table->string('building_no');
            $table->string('street_address');
            $table->string('floor_no');
            $table->string('apartment_no');
            $table->text('additional_directions');
            $table->tinyInteger('is_default')->default(0);
            $table->timestamps();

            // $table->foreign('customer_id')->references('id')->on('customers');
            // $table->foreign('type_id')->references('id')->on('address_types');
            // $table->foreign('location_id')->references('id')->on('locations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_addresses');
    }
}
