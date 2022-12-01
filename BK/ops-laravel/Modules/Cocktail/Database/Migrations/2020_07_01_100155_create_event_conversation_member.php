<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventConversationMember extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('event_conversation_user', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('conversation_uuid');
            $table->unsignedInteger('user_id');
            $table->string('user_type')->comment('morph relation for ops user and event external user');
            $table->timestamps();
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('event_conversation_user');
    }
}
