<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectTimelineOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_timeline_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('wid', false, true)->length(11)->index();
            $table->integer('user_id', false, true)->length(11)->index();
            $table->integer('project_id', false, true)->length(11)->index();
            $table->integer('order', false, true)->length(11);
           $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_timeline_order');
    }
}
