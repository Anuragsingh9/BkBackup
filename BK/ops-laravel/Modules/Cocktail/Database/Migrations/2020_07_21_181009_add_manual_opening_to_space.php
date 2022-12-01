<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddManualOpeningToSpace extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('event_space', function (Blueprint $table) {
            $table->tinyInteger('follow_main_opening_hours')->after('opening_hours')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('event_space', function (Blueprint $table) {
            $table->dropColumn('follow_main_opening_hours');
        });
    }
}
