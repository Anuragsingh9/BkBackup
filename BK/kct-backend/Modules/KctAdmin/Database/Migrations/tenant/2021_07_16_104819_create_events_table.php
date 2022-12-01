<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('events', function (Blueprint $table) {
            $table->uuid('event_uuid')->primary();
            $table->string('title', 255);
            $table->text('header_text')->nullable();
            $table->text('header_line_1')->nullable();
            $table->text('header_line_2')->nullable();
            $table->text('description')->nullable();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('join_code')->nullable();
            $table->integer('security_atr_id')->nullable();
            $table->json('event_settings')->nullable();
            $table->text('image')->nullable();
            $table->integer('type')->comment('(1. Virtual)')->default(1);
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->tinyInteger('manual_opening')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by_user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('events');
    }
}
