<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogUserData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_user_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('log_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->json('user_data')->nullable();
            $table->string('event_uuid')->nullable();
            $table->string('conversation_uuid')->nullable();
            $table->timestamps();

            $table->foreign('log_id')->references('id')->on('logs')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('event_uuid')->references('event_uuid')->on('events');
            $table->foreign('conversation_uuid')->references('uuid')->on('kct_conversations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_user_data');
    }
}
