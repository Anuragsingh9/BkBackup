<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrmFilterConditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crm_filter_conditions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('component', 20);
            $table->string('condition', 20);
            $table->string('condition_type', 10)->default('and');
            $table->string('value', 255);
            $table->string('field_default', 50)->default(null);
            $table->string('field_custom', 50)->default(null);
            $table->integer('filter_type_id');
            $table->integer('filter_id');
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
        /*Schema::table('crm_filter_conditions', function (Blueprint $table) {
            $table->dropForeign(['filter_type_id', 'filter_id']);
            $table->dropIndex(['filter_type_id', 'filter_id']);
        });*/
        Schema::dropIfExists('crm_filter_conditions');
    }
}
