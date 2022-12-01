<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecurrenceIdEventUserLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('event_user_join_reports', function (Blueprint $table) {
            $table->uuid('recurrence_uuid')
                ->nullable()
                ->after('event_uuid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('event_user_join_reports', function (Blueprint $table) {
            $table->dropColumn('recurrence_uuid');
        });
    }
}
