<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSourceColumnToMedia extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('im_message_medias', function (Blueprint $table) {
            $table->string('source')->after('media_url')->comment('workshop doc or system uploaded')->default('system');
            $table->string('title')->after('id')->nullable();
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('im_message_medias', function (Blueprint $table) {
            $table->dropColumn('source');
            $table->dropColumn('title');
        });
    }
}
