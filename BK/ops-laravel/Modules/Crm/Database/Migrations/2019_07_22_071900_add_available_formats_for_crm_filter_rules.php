<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAvailableFormatsForCrmFilterRules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crm_filter_rules', function (Blueprint $table) {
            $table->longText('available_formats')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('crm_filter_rules', function (Blueprint $table) {
            $table->dropColumn('available_formats');
        });
    }
}
