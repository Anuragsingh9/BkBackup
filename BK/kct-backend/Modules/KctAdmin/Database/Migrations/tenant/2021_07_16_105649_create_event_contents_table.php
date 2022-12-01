<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventContentsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('event_contents', function (Blueprint $table) {
            $table->integer('content_type_id')->primary();
            $table->uuid('event_uuid');
            $table->string('content_id', 191);
            $table->json('content_settings');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('event_uuid')->references('event_uuid')->on('events')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('event_contents');
    }
}
