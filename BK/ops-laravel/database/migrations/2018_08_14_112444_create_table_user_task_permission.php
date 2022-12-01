<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserTaskPermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_task_permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id', false, true)->length(11)->index();
            $table->integer('workshop_id', false, true)->length(11)->index();
            $table->integer('task_id', false, true)->length(11)->index();
            $table->string('action_type',50);
            $table->tinyInteger('status')->default(0);
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
        Schema::dropIfExists('user_task_permissions');
    }
}
