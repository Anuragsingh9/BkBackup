<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldWithMealPresenceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumns('presences', ['with_meal_status'])){

            Schema::table('presences', function (Blueprint $table) {
            $table->tinyInteger('with_meal_status')->default(0)->comment('0=not considered 1=RegisterWithLunch 2=RegisterWithoutLunch 3=RegisterWithDinner 4=RegisterWithOutDinner');
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
        Schema::table('presences', function (Blueprint $table) {
            //
        });
    }
}
