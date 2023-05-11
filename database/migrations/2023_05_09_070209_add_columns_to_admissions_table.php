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
        Schema::table('admissions', function (Blueprint $table) {
            $table->unsignedBigInteger('admission_payment_id')->nullable();
            $table->unsignedBigInteger('apply_payment_id')->nullable();
            $table->unsignedBigInteger('files_id')->nullable();
           
            $table->foreign('files_id')->references('id')->on('students_files_details');
            $table->string('apply_payment_status')->nullable();
            $table->renameColumn('payment_status', 'admission_payment_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admissions', function (Blueprint $table) {
            //
        });
    }
};
