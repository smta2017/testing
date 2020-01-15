<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('customer_id')->unsigned();
            $table->string('first_name','100');
            $table->string('last_name','100');
            $table->string('phone','20');
            $table->string('country_code','10');
            $table->string('email','100');
            $table->string('password','100');
            $table->float('cancellation_credit');
            $table->float('reschedule_credit');
            $table->string('is_verified','100');
            $table->integer('referred_by');
            $table->integer('is_archive');
            $table->integer('is_signedOut');
            $table->timestamps();
            //rest of fields then...
            $table->foreign('customer_id')->references('id')->on('customers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_details');
    }
}
