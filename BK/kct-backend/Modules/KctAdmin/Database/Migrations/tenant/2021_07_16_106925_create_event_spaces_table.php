<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventSpacesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('event_spaces', function (Blueprint $table) {
            $table->uuid('space_uuid')->primary();
            $table->string('space_name', 191);
            $table->string('space_short_name', 191)->nullable();
            $table->string('space_mood', 191)->nullable();
            $table->integer('max_capacity')->nullable();
            $table->integer('is_vip_space')->default(0);
            $table->integer('is_duo_space')->default(0);
            $table->integer('is_mono_space')->default(0);
            $table->uuid('event_uuid');
            $table->string('order_id', 50);
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
        Schema::dropIfExists('event_spaces');
    }
}
