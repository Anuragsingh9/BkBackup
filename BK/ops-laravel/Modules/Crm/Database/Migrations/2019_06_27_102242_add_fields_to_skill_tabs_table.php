<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToSkillTabsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('skill_tabs', function (Blueprint $table) {
            $table->tinyInteger('tab_type')->comment('0->User,1->Contact,2->Company,4->Instance,5->Union')->default(0);
            $table->json('visible')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('skill_tabs', function (Blueprint $table) {
            $table->dropColumn('tab_type');
            $table->dropColumn('visible');
        });
    }
}
