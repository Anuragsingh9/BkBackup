<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlertEventsAddEventType extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('events', function (Blueprint $blueprint) {
            $blueprint->integer('event_type')
                ->after('type')
                ->nullable(false)
                ->default(1)
                ->comment('1. Cafeteria, 2. Executive, 3. manager');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('events', function (Blueprint $blueprint) {
            $blueprint->dropColumn('event_type');
        });
    }
}
