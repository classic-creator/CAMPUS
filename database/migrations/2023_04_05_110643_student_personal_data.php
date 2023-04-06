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
        //\
        Schema::create('student_personal_data', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('student_id');
        $table->foreign('student_id')->references('id')->on('users');
        $table->string('first_name');
        $table->string('middle_name')->nullable();
        $table->string('last_name');
        $table->string('father_name');
        $table->string('mother_name');
        $table->string('dob');
        $table->string('email');
        $table->string('phon_no');
        $table->string('identification');
        $table->string('identification_no');
        $table->string('qualification');
        $table->string('mark_obtain_lastExam');
        
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
        //
        Schema::dropIfExists('student_personal_data');
    }
};
