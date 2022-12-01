<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddValueToLabelsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('labels', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
        });
        Schema::table('label_locales', function (Blueprint $table) {
            $table->unsignedBigInteger('group_id')->nullable()->after('id');
            $table->foreign('group_id')->on('groups')->references('id')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('labels', function (Blueprint $table) {
            $table->dropColumn('name');
        });
        Schema::table('label_locales', function (Blueprint $table) {
            $table->dropForeign('group_id');
            $table->dropColumn('group_id');
        });
    }
}
