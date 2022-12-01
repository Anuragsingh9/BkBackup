<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCurrentConvDmy extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('event_dummy_users', function (Blueprint $table) {
            $table->string('current_conv_uuid')->nullable();
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('event_dummy_users', function (Blueprint $table) {
            $table->dropColumn('current_conv_uuid');
        });
    }
}
