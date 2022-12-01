<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKctSpaceUsersTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('kct_space_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->uuid('space_uuid');
            $table->integer('role')->comment('(1. Host), (2. Member)');
            $table->uuid('current_conversation_uuid')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('space_uuid')->references('space_uuid')->on('event_spaces')->cascadeOnDelete();
            $table->foreign('current_conversation_uuid')->references('uuid')->on('kct_conversations')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('kct_space_users');
    }
}
