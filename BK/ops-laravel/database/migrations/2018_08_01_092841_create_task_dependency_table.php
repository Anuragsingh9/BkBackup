<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskDependencyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_dependencies', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id', false, true)->length(11)->index();
            $table->integer('child_id', false, true)->length(11)->index();
            $table->integer('created_by_id', false, true)->length(11)->index();
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
        Schema::dropIfExists('task_dependencies');
    }
}
