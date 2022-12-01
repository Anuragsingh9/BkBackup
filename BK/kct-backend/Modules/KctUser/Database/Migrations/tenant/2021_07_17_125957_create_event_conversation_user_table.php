<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventConversationUserTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('kct_conversation_users', function (Blueprint $table) {
            $table->id();
            $table->uuid('conversation_uuid');
            $table->unsignedBigInteger('user_id');
            $table->dateTime('leave_at')->nullable();
            $table->longText('chime_attendee');
            $table->timestamps();

            $table->foreign('conversation_uuid')->references('uuid')->on('kct_conversations')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('kct_conversation_users');
    }
}
