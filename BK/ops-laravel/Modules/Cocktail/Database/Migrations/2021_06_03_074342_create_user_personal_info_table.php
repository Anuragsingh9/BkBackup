<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserPersonalInfoTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('event_user_personal_info', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->text('desc');
            $table->text('looking_for');
            $table->text('question');
            $table->timestamps();
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('event_user_personal_info');
    }
}
