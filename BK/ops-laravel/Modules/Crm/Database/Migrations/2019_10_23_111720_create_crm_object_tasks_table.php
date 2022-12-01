<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrmObjectTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crm_object_tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->Integer('task_id')->index();
            $table->morphs('crm_object_tasksable', 'crm_object_tasksable');
            $table->timestamps();
            //$table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('crm_object_tasks');
    }
}
