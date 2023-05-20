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
        Schema::create('university_requests', function (Blueprint $table) {
            $table->id();
            $table->string('collegeName');
            $table->string('address');
            $table->string('email')->unique();
            $table->string('rating')->nullable();
            $table->string('description')->nullable();
            $table->unsignedBigInteger('create-by');
            $table->foreign('create-by')->references('id')->on('users');
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
        Schema::dropIfExists('university_requests');
    }
};
