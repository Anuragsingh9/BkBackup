<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDummyUsersTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('dummy_users', function (Blueprint $table) {
            $table->id();
            $table->string('fname')->nullable();
            $table->string('lname')->nullable();
            $table->string('avatar')->nullable();
            $table->string('company')->nullable();
            $table->string('company_position')->nullable();
            $table->string('union')->nullable();
            $table->string('union_position')->nullable();
            $table->string('video_url')->nullable();
            $table->integer('type')->nullable()->comment('1. Regular');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('dummy_users');
    }
}
