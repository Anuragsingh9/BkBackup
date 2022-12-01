<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsDuoSpaceInSpace extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('event_space', function (Blueprint $table) {
            $table->tinyInteger('is_duo_space')->default(0)->after('is_vip_space');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('event_space', function (Blueprint $table) {
            $table->dropColumn('is_duo_space');
        });
    }
}
