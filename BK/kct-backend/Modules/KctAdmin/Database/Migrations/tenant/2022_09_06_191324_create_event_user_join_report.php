<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventUserJoinReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_user_join_reports', function (Blueprint $table) {
            $table->id();
            $table->uuid('event_uuid');
            $table->unsignedBigInteger('user_id');
            $table->dateTime('on_leave')->nullable();
            $table->timestamps();

            $table->foreign('event_uuid')->references('event_uuid')->on('events');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_user_join_reports');
    }
}
