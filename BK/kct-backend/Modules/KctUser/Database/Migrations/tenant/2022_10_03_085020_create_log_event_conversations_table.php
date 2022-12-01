<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogEventConversationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_event_conversations', function (Blueprint $table) {
            $table->id();
            $table->uuid('rec_uuid');
            $table->uuid('space_uuid');
            $table->uuid('convo_uuid');
            $table->dateTime('convo_start');
            $table->dateTime('convo_end')->nullable();
            $table->timestamps();

            $table->foreign('rec_uuid')->on('event_single_recurrences')->references('recurrence_uuid')->cascadeOnDelete();
            $table->foreign('space_uuid')->on('event_spaces')->references('space_uuid')->cascadeOnDelete();
            $table->foreign('convo_uuid')->on('kct_conversations')->references('uuid')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_event_conversations');
    }
}
