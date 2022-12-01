<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogEventContents extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('log_event_contents', function (Blueprint $table) {
            $table->id();
            $table->uuid('recurrence_uuid');
            $table->integer('action')->comment('1. Zoom, 2.Video, 3.Image, 4.Network, 5.Networking Mute, 6. Content');
            $table->string('action_state')->comment('To store the respective action data, e.g. For video -> video link');
            $table->dateTime('start_time');
            $table->unsignedBigInteger('duration')->nullable();
            $table->timestamps();

            $table->foreign('recurrence_uuid')->on('event_single_recurrences')->references('recurrence_uuid')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('log_event_contents');
    }
}
