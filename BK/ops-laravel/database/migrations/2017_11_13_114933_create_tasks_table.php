<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTasksTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('tasks', function(Blueprint $table) {
            $table->integer('id', true);
            $table->integer('workshop_id');
            $table->string('meeting_id', 40)->nullable();
            $table->integer('topic_id');
            $table->integer('task_created_by_id');
            $table->string('task_text', 250);
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('milestone_id');
            $table->integer('assign_for')->default(0)->comment('1=workshop all users 0=some users');
            $table->string('status', 50)->default('0')->comment('0=pending/1=completed/2=behind schedule');
            $table->datetime('updated_at')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('tasks');
    }

}
