<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeBluejeansIdToString extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('event_info', function (Blueprint $table) {
            $table->string('bluejeans_id')->change();
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('event_info', function (Blueprint $table) {
            $table->unsignedInteger('bluejeans_id')->change();
        });
    }
}
