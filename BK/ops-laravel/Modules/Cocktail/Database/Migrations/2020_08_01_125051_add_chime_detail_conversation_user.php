<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddChimeDetailConversationUser extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('event_conversation_user', function (Blueprint $table) {
            $table->json('chime_attendee')->nullable();
//            $table->string('chime_join_token')->nullable();
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('event_conversation_user', function (Blueprint $table) {
            $table->dropColumn('chime_attendee');
//            $table->dropColumn('chime_join_token');
        });
    }
}
