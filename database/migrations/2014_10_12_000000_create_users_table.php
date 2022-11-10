<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->ulid('id');
            $table->string('firstName');
            $table->string('lastName');
            $table->string('userName');
            $table->unsignedTinyInteger('age');
            $table->string('address');
            $table->unsignedSmallInteger('points');
            $table->string('email')->unique();
            $table->string('password');
            $table->foreignUlid('access_level_id');
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
};
