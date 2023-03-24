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
            $table->string('college_preference_1')->nullable();
            $table->string('college_preference_2')->nullable();
            $table->string('college_preference_3')->nullable();
            $table->string('depertment_preference_1')->nullable();
            $table->string('depertment_preference_2')->nullable();
            $table->string('depertment_preference_3')->nullable();
            $table->string('course_preference_1')->nullable();
            $table->string('course_preference_2')->nullable();
            $table->string('course_preference_3')->nullable();
            $table->string('address_preference_1')->nullable();
            $table->string('address_preference_2')->nullable();
            $table->string('address_preference_3')->nullable();
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
