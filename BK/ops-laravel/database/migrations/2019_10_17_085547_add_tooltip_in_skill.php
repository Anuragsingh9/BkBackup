<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTooltipInSkill extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('skills', 'tip_en')) {
            Schema::table('skills', function (Blueprint $table) {
                $table->renameColumn('tip_en', 'tooltip_en');
            });
        }
        if (Schema::hasColumn('skills', 'tip_fr')) {
            Schema::table('skills', function (Blueprint $table) {
                $table->renameColumn('tip_fr', 'tooltip_fr');
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
        //
    }
}
