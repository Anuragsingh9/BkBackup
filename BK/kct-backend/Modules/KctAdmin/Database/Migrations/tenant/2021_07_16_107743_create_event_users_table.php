<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventUsersTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('event_users', function (Blueprint $table) {
            $table->id();
            $table->uuid('event_uuid');
            $table->unsignedBigInteger('user_id');
            $table->integer('event_user_role')->nullable()->comment('1 Team, 2 Expert');
            $table->tinyInteger('is_presenter')->default(0);
            $table->tinyInteger('is_moderator')->default(0);
            $table->integer('is_vip')->default(0);
            $table->integer('is_organiser')->default(0);
            $table->string('state')->default(1)->comment('(1.Available), (2.DND)');
            $table->integer('is_joined_after_reg')->default(1);
            $table->integer('presence');
            $table->timestamps();

            $table->foreign('event_uuid')->references('event_uuid')->on('events')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('event_users');
    }
}
