<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeEventUuidToSpaceUuid extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('event_conversation', function (Blueprint $table) {
            $table->renameColumn('event_uuid', 'space_uuid')->change();
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('event_conversation', function (Blueprint $table) {
//            $table->renameColumn('space_uuid', 'event_uuid')->change();
        });
    }
}
