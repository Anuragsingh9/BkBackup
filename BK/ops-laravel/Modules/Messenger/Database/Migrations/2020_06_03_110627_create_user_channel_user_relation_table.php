<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserChannelUserRelationTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('im_user_channel_user', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user1_id');
            $table->unsignedInteger('user2_id');
            $table->uuid('channel_uuid');
            $table->timestamps();
            $table->softDeletes();
            
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('im_user_channel_user');
    }
}
