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
        Schema::create('lendings', function (Blueprint $table) {
            $table->ulid('id');
            $table->foreignUlid('book_id');
            $table->foreignUlid('user_id');
            $table->dateTimeTz('date borrowed');
            $table->dateTimeTz('date due');
            $table->dateTimeTz('date returned')->nullable();
            $table->unsignedSmallInteger('points');
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
        Schema::dropIfExists('lendings');
    }
};
