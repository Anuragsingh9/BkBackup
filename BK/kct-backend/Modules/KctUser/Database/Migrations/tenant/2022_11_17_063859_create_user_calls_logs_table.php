<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserCallsLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_calls_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('rec_uuid');
            $table->uuid('space_uuid');
            $table->uuid('conv_uuid');
            $table->unsignedBigInteger('caller_id');
            $table->unsignedBigInteger('call_type')->comment('( 1. Missed 2. Rejected 3. Connected )');
            $table->timestamps();

            $table->foreign('rec_uuid')->on('event_single_recurrences')->references('recurrence_uuid')->cascadeOnDelete();
            $table->foreign('space_uuid')->on('event_spaces')->references('space_uuid')->cascadeOnDelete();
            $table->foreign('conv_uuid')->on('kct_conversations')->references('uuid')->cascadeOnDelete();
            $table->foreign('caller_id')->on('users')->references('id')->cascadeOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_calls_logs');
    }
}
