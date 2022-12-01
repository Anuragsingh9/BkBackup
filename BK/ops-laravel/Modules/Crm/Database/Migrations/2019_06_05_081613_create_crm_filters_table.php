<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrmFiltersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crm_filters', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->integer('filter_type_id');
            $table->integer('created_by');
            $table->datetime('updated_at')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /*chema::table('crm_filters', function (Blueprint $table) {
            $table->dropForeign(['filter_type_id', 'created_by']);
            $table->dropIndex(['filter_type_id', 'created_by']);
        });*/
        Schema::dropIfExists('crm_filters');
    }
}
