<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configs', function (Blueprint $table) {
            $table->string('id',5)->primary();
            $table->string('corporatename',100);
            $table->string('phone',50)->nullable();
            $table->string('fax',50)->nullable();
            $table->string('email',50)->nullable();
            $table->string('facebook',50)->nullable();
            $table->string('whatsapp',50)->nullable();
            $table->text('about')->nullable();
            $table->text('transmethod')->nullable();
            $table->string('imglogo',50)->nullable();
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
        Schema::dropIfExists('configs');
    }
}
