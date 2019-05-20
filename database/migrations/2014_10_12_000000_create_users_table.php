<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->increments('id');
            $table->string('email', 100);
            $table->string('password',100)->nullable();
            $table->integer('role')->nullable();
            $table->string('first_name',100)->nullable();
            $table->string('last_name',100)->nullable();
            $table->integer('gender_id')->nullable();
            $table->timestamp('birthday')->nullable();
            $table->string('postcode', 100)->nullable();
            $table->string('address', 100)->nullable();
            $table->string('phone', 100)->nullable();
            $table->boolean('activated')->default(0);
            $table->timestamp('last_access')->nullable();
            $table->integer('attempt')->default(0);
            $table->string('remember_token',100)->nullable();
            $table->string('token',100)->nullable();
            $table->string('reset_pass_token',100)->nullable();
            $table->integer('token_expire')->nullable();
            $table->unique('email');
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
