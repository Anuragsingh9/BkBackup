<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PersonalMessage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
            Schema::create('personal_messages', function(Blueprint $table)
            {
            $table->increments('id', true);
            $table->integer('to_user_id');
            $table->integer('from_user_id');
            $table->text('message_text');
            $table->boolean('is_read')->default(0);
            $table->boolean('inbox_delete')->default(0);
            $table->boolean('outbox_delete')->default(0);
            $table->datetime('updated_at')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            });
    
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::drop('personal_messages');
    }

}