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
        Schema::table('seat_structures', function (Blueprint $table) {
            $table->string('total_seat');
            $table->string('OBC');
            $table->string('SC');
            $table->string('ST');
            $table->string('EWS');
            $table->string('other');
            $table->string('student_id');
            $table->unsignedBigInteger('course_id');
            $table->foreign('course_id')->references('id')->on('courses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('seat_structures', function (Blueprint $table) {
            //
        });
    }
};
