<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('task_comments')) {
            Schema::rename('task_comments', 'comments');
        }

        Schema::table('comments', function (Blueprint $table) {
            $table->integer('task_id')->nullable()->change();
            $table->integer('user_id')->nullable()->change();
            $table->integer('created_by');
            $table->nullableMorphs('commentable');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('comments', function (Blueprint $table) {

        });
    }
}
