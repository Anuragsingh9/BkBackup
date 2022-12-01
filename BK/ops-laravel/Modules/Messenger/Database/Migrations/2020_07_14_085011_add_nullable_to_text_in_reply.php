<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNullableToTextInReply extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('im_message_replies', function (Blueprint $table) {
            $table->string('reply_text')->nullable()->change();
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('im_message_replies', function (Blueprint $table) {
            $table->string('reply_text')->nullable(FALSE)->change();
        });
    }
}
