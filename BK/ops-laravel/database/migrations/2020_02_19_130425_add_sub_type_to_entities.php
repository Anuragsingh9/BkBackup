<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSubTypeToEntities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entities', function(Blueprint $table) {
            $table->tinyInteger('entity_sub_type')->nullable()->comment('For Union, (1. Internal), (2, External)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('entities', function(Blueprint $table) {
            $table->dropColumn('entity_sub_type');
        });
    }
}
