<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveUserTypeColumn extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('event_conversation_user', function (Blueprint $table) {
            $table->dropColumn('user_type');
        });
        Schema::table('event_conversation', function (Blueprint $table) {
            $table->primary('uuid');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('event_conversation_user', function (Blueprint $table) {
            $table->string('user_type')->nullable();
        });
        Schema::table('event_conversation', function(Blueprint $table) {
            $table->dropPrimary('uuid');
        });
    }
}
