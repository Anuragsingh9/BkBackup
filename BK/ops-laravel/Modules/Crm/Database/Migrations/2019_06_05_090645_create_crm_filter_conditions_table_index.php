<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrmFilterConditionsTableIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crm_filter_conditions', function (Blueprint $table) {
            $table->unsignedInteger('filter_type_id')->change();
            $table->unsignedInteger('filter_id')->change();
            $table->index(['filter_type_id', 'filter_id']);
            $table->foreign('filter_type_id')
                ->references('id')->on('crm_filter_types')->onDelete('cascade');

            $table->foreign('filter_id')
                ->references('id')->on('crm_filters')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /*Schema::table('crm_filter_conditions', function (Blueprint $table) {
            $table->dropForeign(['filter_type_id', 'filter_id']);
            $table->dropIndex(['filter_type_id', 'filter_id']);
        });*/
    }
}
