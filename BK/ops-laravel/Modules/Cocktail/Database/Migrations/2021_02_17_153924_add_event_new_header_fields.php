<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEventNewHeaderFields extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('event_info', function (Blueprint $table) {
            $table->text('header_line_2')->nullable()->after('header_text');
            $table->text('header_line_1')->nullable()->after('header_text');
            $table->text('header_text')->nullable()->change();
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('event_info', function (Blueprint $table) {
            $table->dropColumn('header_line_2');
            $table->dropColumn('header_line_1');
            $table->text('header_text')->nullable(false)->change();
        });
    }
}
