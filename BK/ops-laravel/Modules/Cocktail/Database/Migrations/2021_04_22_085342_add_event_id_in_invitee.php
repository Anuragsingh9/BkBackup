<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEventIdInInvitee extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('event_user_invites', function (Blueprint $table) {
            $table->uuid('event_uuid')->nullable()->after('invited_by_user_id');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('event_user_invites', function (Blueprint $table) {
            $table->dropColumn('invited_by_user_id');
        });
    }
}
