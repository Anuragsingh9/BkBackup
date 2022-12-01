<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventUserRoleTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('event_user_roles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_user_id');
            $table->integer('role')->comment('(3-Moderator, 4-Speaker');
            $table->unsignedBigInteger('moment_id')->nullable();
            $table->uuid('space_uuid')->nullable();

            $table->foreign('space_uuid')->references('space_uuid')->on('event_spaces')->cascadeOnDelete();
            $table->foreign('event_user_id')->references('id')->on('event_users')->cascadeOnDelete();
            $table->foreign('moment_id')->references('id')->on('event_moments')->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('event_user_roles');
    }
}
