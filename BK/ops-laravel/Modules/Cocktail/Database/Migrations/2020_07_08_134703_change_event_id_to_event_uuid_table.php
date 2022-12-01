<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeEventIdToEventUuidTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('event_space', function (Blueprint $table) {
            $table->string('event_id')->change();
            $table->renameColumn('event_id', 'event_uuid');
        });
        Schema::table('event_user_data', function (Blueprint $table) {
            $table->string('event_id')->change();
            $table->renameColumn('event_id', 'event_uuid');
        });
        Schema::table('event_conversation', function (Blueprint $table) {
            $table->string('event_id')->change();
            $table->renameColumn('event_id', 'event_uuid');
        });
        Schema::table('event_external_user', function (Blueprint $table) {
            $table->string('event_id')->change();
            $table->renameColumn('event_id', 'event_uuid');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('event_space', function (Blueprint $table) {
            $table->unsignedInteger('event_uuid')->change();
            $table->renameColumn('event_uuid', 'event_id');
        });
        Schema::table('event_user_data', function (Blueprint $table) {
            $table->unsignedInteger('event_uuid')->change();
            $table->renameColumn('event_uuid', 'event_id');
        });
        Schema::table('event_conversation', function (Blueprint $table) {
            $table->unsignedInteger('event_uuid')->change();
            $table->renameColumn('event_uuid', 'event_id');
        });
        Schema::table('event_external_user', function (Blueprint $table) {
            $table->unsignedInteger('event_uuid')->change();
            $table->renameColumn('event_uuid', 'event_id');
        });
    }
}
