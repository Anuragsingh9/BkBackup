<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldMealMeetingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumns('meetings', ['with_meal'])){
        Schema::table('meetings', function (Blueprint $table) {
            $table->tinyInteger('with_meal')->default(0)->comment('0=meeting Registration off 1=meal 2=withourMeal 3=withLunch 4=withDinner');
        });
    }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('meetings', function (Blueprint $table) {
            //
        });
    }
}
