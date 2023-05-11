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
        Schema::create('students_files_details', function (Blueprint $table) {
            $table->id();
            $table->string('profile_photo');
            $table->string('aadhar');
            $table->string('signature');
            $table->string('hslc_registation');
            $table->string('hslc_marksheet');
            $table->string('hslc_certificate');
            $table->string('hslc_admit');
            $table->string('hsslc_registation');
            $table->string('hsslc_marksheet');
            $table->string('hsslc_certificate');
            $table->string('hsslc_admit');
            
           
            $table->unsignedBigInteger('student_id');
            $table->foreign('student_id')->references('id')->on('users');
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
        Schema::dropIfExists('students_files_details');
    }
};
