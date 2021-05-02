<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->boolean('sale');
            $table->boolean('rent');

            $table->bigInteger('owner')->unsigned();
            $table->boolean('owner_spouse')->nullable();
            $table->bigInteger('owner_company')->unsigned()->nullable();

            $table->bigInteger('acquirer')->unsigned();
            $table->boolean('acquirer_spouse')->nullable();
            $table->bigInteger('acquirer_company')->unsigned()->nullable();

            $table->bigInteger('property')->unsigned();
            $table->double('price');
            $table->double('tribute');
            $table->double('condominium');

            $table->unsignedInteger('due_date');
            $table->unsignedInteger('deadline');
            $table->date('start_at');

            $table->timestamps();

            $table->foreign('owner')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('owner_company')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('acquirer')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('acquirer_company')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('property')->references('id')->on('properties')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contracts');
    }
}
