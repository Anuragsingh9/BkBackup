<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventRecurrencesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('event_single_recurrences', function (Blueprint $table) {
            $table->uuid('recurrence_uuid')->primary();
            $table->uuid('event_uuid');
            $table->integer('recurrence_count')->default(1);
            $table->dateTime('recurrence_date');
            $table->timestamps();
            $table->foreign('event_uuid')->on('events')->references('event_uuid')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('event_single_recurrences');
    }
}
