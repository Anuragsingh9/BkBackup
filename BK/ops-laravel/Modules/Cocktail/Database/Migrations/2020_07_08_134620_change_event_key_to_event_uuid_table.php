<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeEventKeyToEventUuidTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::connection('tenant')->statement('ALTER TABLE event_info CHANGE type type VARCHAR(200)');
        Schema::table('event_info', function (Blueprint $table) {
            $table->renameColumn('event_key', 'event_uuid');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('event_info', function (Blueprint $table) {
            $table->renameColumn('event_uuid', 'event_key');
        });
    }
}
