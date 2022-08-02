<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->string('id',50)->primary();
            $table->string('name',50);
            $table->date('datebirth');
            $table->string('address',100)->nullable();
            $table->char('idprovince',2)->nullable();
            $table->char('idregency',5)->nullable();
            $table->char('iddistrict',8)->nullable();
            $table->string('postalcode',5)->nullable();
            $table->string('phone',50)->nullable();
            $table->string('email',50);
            $table->string('password');
            $table->string('idroleuser',2);
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
