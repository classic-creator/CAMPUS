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
        Schema::create('student_educational_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->foreign('student_id')->references('id')->on('users');
            $table->string('class10_passingYear');
            $table->string('class10_roll');
            $table->string('class10_no');
            $table->string('class10_board');
            $table->string('class10_school');
            $table->string('class10_totalMark');
            $table->string('class10_markObtain');
            $table->string('class12_passingYear');
            $table->string('class12_roll');
            $table->string('class12_no');
            $table->string('class12_board');
            $table->string('class12_college');
            $table->string('class12_strem');
            $table->string('class12_totalMark');
            $table->string('class12_markObtain');
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
        Schema::dropIfExists('student_educational_details');
    }
};
