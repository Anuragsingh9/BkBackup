<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventRecurrences extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_recurrences', function (Blueprint $table) {
            $table->id();
            $table->uuid('event_uuid');
            $table->integer('recurrence_type')->comment('(1. Daily, 2. Weekdays, 3. Weekly, 4. Bimonthly, 5. Monthly)');
            $table->dateTime('end_date');
            $table->json('recurrences_settings')->nullable();
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
        Schema::dropIfExists('event_recurrences');
    }
}
