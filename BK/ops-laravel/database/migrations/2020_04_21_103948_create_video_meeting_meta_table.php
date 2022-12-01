<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVideoMeetingMetaTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('meeting_meta', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('meeting_id');
            $table->string('video_meeting_id')->nullable()->comment('Bluejeans video meeting id');
            $table->string('video_meeting_numeric_id')->nullable()->comment('Bluejeans video meeting id');
            $table->string('video_meeting_user_id')->nullable()->comment('Bluejeans meeting user');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('meeting_meta');
    }
}
