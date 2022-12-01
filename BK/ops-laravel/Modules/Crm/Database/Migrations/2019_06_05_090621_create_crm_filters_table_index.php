<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrmFiltersTableIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crm_filters', function (Blueprint $table) {
            $table->unsignedInteger('filter_type_id')->change();
            $table->unsignedInteger('created_by')->change();
            $table->index(['filter_type_id', 'created_by']);

            $table->foreign('filter_type_id')
                ->references('id')
                ->on('crm_filter_types')
                ->onDelete('cascade');

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /*Schema::table('crm_filters', function (Blueprint $table) {
            $table->dropForeign(['filter_type_id', 'created_by']);
            $table->dropIndex(['filter_type_id', 'created_by']);
        });*/
    }
}
