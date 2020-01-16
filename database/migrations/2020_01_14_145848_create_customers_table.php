<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('first_name','100');
            $table->string('last_name','100');
            $table->string('password');
            $table->string('email')->unique();
            $table->string('phone','20');
            $table->string('country_code','10');
            $table->boolean('is_super')->default(false);
            $table->rememberToken();
            $table->float('cancellation_credit')->default(0);
            $table->float('reschedule_credit')->default(0);
            $table->string('is_verified','100')->default('0');
            $table->integer('referred_by')->default(0);
            $table->integer('is_archive')->default(0);
            $table->integer('is_signedOut')->default(0);
            $table->string('jwt_token','600');
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
        Schema::dropIfExists('customers');
    }
}
