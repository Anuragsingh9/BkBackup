<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsCrmFilterConditions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('crm_filter_conditions', function (Blueprint $table) {
            $table->string('field_name', 50)->nullable();
            $table->boolean('is_default')->nullable();
            $table->integer('field_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('crm_filter_conditions', function (Blueprint $table) {
            $table->dropColumn('field_name');
            $table->dropColumn('is_default');
            $table->dropColumn('field_id');
        });
    }
}
