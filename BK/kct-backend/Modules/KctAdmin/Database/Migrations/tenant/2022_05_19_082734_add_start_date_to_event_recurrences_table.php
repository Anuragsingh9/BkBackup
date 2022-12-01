<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStartDateToEventRecurrencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('event_recurrences', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('recurrence_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('event_recurrences', function (Blueprint $table) {
            $table->dropColumn('start_date');
        });
    }
}
