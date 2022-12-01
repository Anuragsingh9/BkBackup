<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogEventActionCounts extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('log_event_action_counts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->uuid('recurrence_uuid');
            $table->integer('conv_count')->default(0);
            $table->integer('reg_count')->default(0);
            $table->integer('attendee_count')->default(0);
            $table->integer('p_image_count')->default(0);
            $table->integer('p_video_count')->default(0);
            $table->integer('p_zoom_count')->default(0);
            $table->integer('sh_conv_count')->default(0);
            $table->timestamps();

            $table->foreign('recurrence_uuid')->on('event_single_recurrences')->references('recurrence_uuid')->cascadeOnDelete();
            $table->foreign('group_id')->on('groups')->references('id')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('log_event_action_counts');
    }
}
