<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSpaceHostKeyConversations extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('kct_conversations', function (Blueprint $table) {
            $table->integer('is_host')->after('is_private')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('kct_conversations', function (Blueprint $table) {
            $table->dropColumn('is_host');
        });
    }
}
