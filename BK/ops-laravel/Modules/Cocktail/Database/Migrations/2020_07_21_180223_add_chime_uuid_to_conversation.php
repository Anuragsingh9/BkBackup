<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddChimeUuidToConversation extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('event_conversation', function (Blueprint $table) {
            $table->uuid('aws_chime_uuid')->after('uuid')->nullable();
            $table->json('aws_chime_meta')->after('aws_chime_uuid')->nullable();
            $table->dateTime('end_at')->after('space_uuid')->nullable();
        });
        
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('event_conversation', function (Blueprint $table) {
            $table->dropColumn('aws_chime_uuid');
            $table->dropColumn('aws_chime_meta');
            $table->dropColumn('end_at');
        });
    }
}
