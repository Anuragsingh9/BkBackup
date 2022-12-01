<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePersonalInfoColumnName extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('event_user_personal_info', function (Blueprint $table) {
            $table->renameColumn('desc', 'field_1');
            $table->renameColumn('looking_for', 'field_2');
            $table->renameColumn('question', 'field_3');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('event_user_personal_info', function (Blueprint $table) {
            $table->renameColumn('field_1', 'desc');
            $table->renameColumn('field_2', 'looking_for');
            $table->renameColumn('field_3', 'question');
        });
    }
}
