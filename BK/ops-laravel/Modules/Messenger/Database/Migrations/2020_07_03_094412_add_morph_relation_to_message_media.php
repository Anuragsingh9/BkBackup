<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMorphRelationToMessageMedia extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('im_message_medias', function (Blueprint $table) {
            $table->renameColumn('message_id', 'attachmentable_id');
            $table->string('attachmentable_type')->comment('to store attachment is of message or reply')->after('message_id');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('im_message_medias', function (Blueprint $table) {
            $table->renameColumn('attachmentable_id', 'message_id');
            $table->dropColumn('attachmentable_type');
        });
    }
}
