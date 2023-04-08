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
        Schema::create('admissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->foreign('student_id')->references('id')->on('users');
            $table->unsignedBigInteger('college_id');
            $table->foreign('college_id')->references('id')->on('universitys');
            $table->unsignedBigInteger('course_id');
            $table->foreign('course_id')->references('id')->on('courses');


            $table->unsignedBigInteger('personalDetails_id');
            $table->foreign('personalDetails_id')->references('id')->on('student_personal_data');

            $table->unsignedBigInteger('educationalDetails_id');
            $table->foreign('educationalDetails_id')->references('id')->on('student_educational_details');

            $table->unsignedBigInteger('address_id');
            $table->foreign('address_id')->references('id')->on('addresses');

            $table->string('admission_status')->default('under-review');
            $table->string('payment_status')->default('panding');

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
        Schema::dropIfExists('admissions');
    }
};
