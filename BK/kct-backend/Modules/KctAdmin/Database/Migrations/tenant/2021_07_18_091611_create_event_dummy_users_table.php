<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventDummyUsersTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('event_dummy_users', function (Blueprint $table) {
            $table->id();
            $table->uuid('event_uuid');
            $table->uuid('space_uuid');
            $table->unsignedBigInteger('dummy_user_id');
            $table->uuid('current_conv_uuid')->nullable();
            $table->timestamps();

            $table->foreign('event_uuid')->references('event_uuid')->on('events')->cascadeOnDelete();
            $table->foreign('space_uuid')->references('space_uuid')->on('event_spaces')->cascadeOnDelete();
            $table->foreign('current_conv_uuid')->references('uuid')->on('kct_conversations')->nullOnDelete();
            $table->foreign('dummy_user_id')->references('id')->on('dummy_users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('event_dummy_users');
    }
}
