<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->string('id',50)->primary();
            $table->date('datetr');
            $table->string('iduser',50);
            $table->string('idcourier',50);
            $table->integer('weight')->default('0');
            $table->string('receiptnumber',50)->nullable();
            $table->decimal('couriercost', $precision = 8, $scale = 2)->default('0');
            $table->string('idbank',50)->nullable();
            $table->integer('uniquecode')->default('0');
            $table->decimal('transfer', $precision = 8, $scale = 2)->default('0');
            $table->integer('status')->default('0');
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
        Schema::dropIfExists('transactions');
    }
}
