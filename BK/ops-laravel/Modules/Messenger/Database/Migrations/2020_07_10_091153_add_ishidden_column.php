<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIshiddenColumn extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('im_user_channel_visits', function (Blueprint $table) {
            $table->tinyInteger('is_hidden')->after('channel_uuid')->nullable();
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('im_user_channel_visits', function (Blueprint $table) {
            $table->dropColumn('is_hidden');
        });
    }
}
