<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventConversationTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('kct_conversations', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('aws_chime_uuid')->nullable();
            $table->longText('aws_chime_meta')->nullable();
            $table->uuid('space_uuid');
            $table->dateTime('end_at')->nullable();
            $table->integer('is_private')->default(0);
            $table->unsignedBigInteger('private_by')->nullable();
            $table->timestamps();

            $table->foreign('private_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('space_uuid')->references('space_uuid')->on('event_spaces')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('kct_conversations');
    }
}
