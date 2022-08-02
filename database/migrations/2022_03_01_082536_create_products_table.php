<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->string('id',50)->primary();
            $table->string('idcategory',50);
            $table->string('productname',100);
            $table->text('productdesc');
            $table->decimal('price', $precision = 8, $scale = 2)->default('0');
            $table->integer('discount')->default('0');
            $table->integer('stock')->default('0');
            $table->integer('weight')->default('0');
            $table->integer('sold')->default('0');
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
        Schema::dropIfExists('products');
    }
}
