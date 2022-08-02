<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckoutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checkouts', function (Blueprint $table) {
            $table->string('id',50)->primary();
            $table->string('idtransaction',50);
            $table->string('idproduct',50);
            $table->decimal('price', $precision = 8, $scale = 2)->default('0');
            $table->integer('discount')->default('0');
            $table->integer('qty');
            $table->integer('weight')->default('0');
            $table->decimal('subtotal', $precision = 8, $scale = 2)->default('0');
            $table->string('remark',50)->nullable();
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
        Schema::dropIfExists('checkouts');
    }
}
