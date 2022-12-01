<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventDummyUser extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('event_dummy_users', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('event_uuid');
            $table->uuid('space_uuid');
            $table->unsignedInteger('dummy_user_id');
            $table->timestamps();
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('event_dummy_users');
    }
}
