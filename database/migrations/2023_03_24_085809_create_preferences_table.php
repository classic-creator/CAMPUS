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
        Schema::create('preferences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->foreign('student_id')->references('id')->on('users');
            $table->string('college1')->nullable();
            $table->string('college2')->nullable();
            $table->string('college3')->nullable();
            $table->string('depertment1')->nullable();
            $table->string('depertment2')->nullable();
            $table->string('depertment3')->nullable();
            $table->string('course1')->nullable();
            $table->string('course2')->nullable();
            $table->string('course3')->nullable();
            $table->string('address')->nullable();
           
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
        Schema::dropIfExists('preferences');
    }
};
