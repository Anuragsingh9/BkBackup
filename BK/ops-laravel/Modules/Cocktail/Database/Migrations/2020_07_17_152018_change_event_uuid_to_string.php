<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeEventUuidToString extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('event_space', function (Blueprint $table) {
            $table->string('event_uuid')->change();
        });
        Schema::table('event_user_data', function (Blueprint $table) {
            $table->string('event_uuid')->change();
        });
        Schema::table('event_conversation', function (Blueprint $table) {
            $table->string('event_uuid')->change();
        });
        Schema::table('event_external_user', function (Blueprint $table) {
            $table->string('event_uuid')->change();
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
        });
        Schema::table('event_user_data', function (Blueprint $table) {
            $table->unsignedInteger('event_uuid')->change();
        });
//        Schema::table('event_conversation', function (Blueprint $table) {
//            $table->unsignedInteger('event_uuid')->change();
//        });
        Schema::table('event_external_user', function (Blueprint $table) {
            $table->unsignedInteger('event_uuid')->change();
        });
    }
}
