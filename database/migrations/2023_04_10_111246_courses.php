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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('courseName');
            $table->string('fees');
            $table->string('duration');
            $table->string('eligibility');
            $table->unsignedBigInteger('college_id');
            $table->foreign('college_id')->references('id')->on('universitys');
            $table->unsignedBigInteger('depertment_id');
            $table->foreign('depertment_id')->references('id')->on('depertments');
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
        Schema::dropIfExists('courses');
    }
};
