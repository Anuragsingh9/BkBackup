<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColoumnAddedToPresenceInSkillTab extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('skill_tabs', function (Blueprint $table) {
            $table->boolean('added_to_presence')->default(false)->comment('allow to add in presence list');
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
            //
        });
    }
}
