<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogConvoSubStateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_convo_sub_state', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('convo_log_id');
            $table->unsignedInteger('users_count');
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->unsignedBigInteger('duration')->nullable();
            $table->timestamps();

            $table->foreign('convo_log_id')->on('log_event_conversations')->references('id')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_convo_sub_state');
    }
}
