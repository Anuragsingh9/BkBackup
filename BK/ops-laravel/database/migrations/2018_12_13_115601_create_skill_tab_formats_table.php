<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSkillTabFormatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
 	if (!Schema::hasTable('skill_tab_formats')) {
        Schema::create('skill_tab_formats', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name_en');
            $table->string('name_fr');
            $table->timestamps();
        });
	}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('skill_tab_formats');
    }
}
