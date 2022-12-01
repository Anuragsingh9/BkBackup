<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChannelMessagesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('im_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('message_text');
            $table->uuid('channel_uuid');
            $table->unsignedInteger('sender_id')->comment('currently using user id but in future it can be polymorphic');
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
        Schema::dropIfExists('im_messages');
    }
}
