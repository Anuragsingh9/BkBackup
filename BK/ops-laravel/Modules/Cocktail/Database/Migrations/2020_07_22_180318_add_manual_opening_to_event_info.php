<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddManualOpeningToEventInfo extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('event_info', function (Blueprint $table) {
            $table->tinyInteger('manual_opening')->after('bluejeans_id')->nullable()->comment('related to space opening');
            $table->tinyInteger('kct_enabled')->after('bluejeans_id')->nullable()->comment('keep contact enabled or not during this time');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('event_info', function (Blueprint $table) {
            $table->dropColumn('manual_opening');
            $table->dropColumn('kct_enabled');
        });
    }
}
