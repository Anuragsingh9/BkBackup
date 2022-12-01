<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldTypeToSkillTabFormat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('skill_tab_formats', function (Blueprint $table) {
            $table->string('short_name', 50)->nullable();
            $table->string('field_type', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('skill_tab_formats', function (Blueprint $table) {
            $table->dropColumn('short_name');
            $table->dropColumn('field_type');
        });
    }
}
