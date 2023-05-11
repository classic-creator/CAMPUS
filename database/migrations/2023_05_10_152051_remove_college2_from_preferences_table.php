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
        Schema::table('preferences', function (Blueprint $table) {
            $table->dropColumn('college2');
            $table->dropColumn('college3');
            
            $table->dropColumn('course2');
            
            $table->dropColumn('course3');
            $table->dropColumn('depertment2');
            $table->dropColumn('depertment3');
            
            $table->dropColumn('address');
            $table->string('college1')->default(NULL)->change();
            $table->string('course1')->default(NULL)->change();
            $table->string('depertment1')->default(NULL)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('preferences', function (Blueprint $table) {
            //
        });
    }
};
