<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterEventMomentsAutoIncrement extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('event_moments', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement()->change();
            $table->string('moment_name')->nullable()->change();
            $table->string('moment_description')->nullable()->change();
            $table->string('moment_settings')->nullable()->change();
            $table->string('moment_id')->nullable()->change();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('event_moments', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->change();
        });
    }
}
