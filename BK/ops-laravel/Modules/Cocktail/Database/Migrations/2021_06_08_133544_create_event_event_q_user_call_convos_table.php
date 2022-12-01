<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventEventQUserCallConvosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_event_q_user_call_convos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_call_id');
            $table->uuid('conversation_uuid');
            $table->string('space_uuid');
            $table->string('event_uuid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_event_q_user_call_convos');
    }
}
