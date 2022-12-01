<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventMetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_metas', function (Blueprint $table) {
            $table->id();
            $table->uuid('event_uuid');
            $table->dateTime('reg_start_time');
            $table->dateTime('reg_end_time');
            $table->integer('event_status')->comment('(1. Live 2. Draft)');
            $table->integer('share_agenda');
            $table->timestamps();

            $table->foreign('event_uuid')->references('event_uuid')->on('events')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_metas');
    }
}
