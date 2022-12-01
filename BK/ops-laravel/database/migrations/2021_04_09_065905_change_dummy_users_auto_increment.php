<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDummyUsersAutoIncrement extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::update("ALTER TABLE dummy_users AUTO_INCREMENT = 5000000;");
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        //
    }
}
