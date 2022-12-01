<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewMessageTypeComment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::table('messages', function (Blueprint $table) {
//            $table->tinyInteger('type')->comment('1. Workshop Message, 2. Personal Message, 3. Channel Message')->change();
//        });
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE messages CHANGE COLUMN `type` `type` tinyInt(4) NOT NULL COMMENT '1. Workshop Message, 2. Personal Message, 3. Channel Message'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('', function (Blueprint $table) {

        });
    }
}
