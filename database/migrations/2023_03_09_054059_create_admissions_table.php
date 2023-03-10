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
            $table->unsignedBigInteger('studentId');
            $table->foreign('studentId')->references('id')->on('users');
            $table->unsignedBigInteger('collegeId');
            $table->foreign('collegeId')->references('id')->on('universitys');
            $table->unsignedBigInteger('courseId');
            $table->foreign('courseId')->references('id')->on('courses');
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
