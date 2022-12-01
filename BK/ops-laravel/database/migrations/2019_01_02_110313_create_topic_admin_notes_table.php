<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopicAdminNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('topic_admin_notes')) {
            Schema::create('topic_admin_notes', function (Blueprint $table) {
                $table->increments('id');
                $table->text('topic_note');
                $table->integer('user_id', false, true);
                $table->integer('topic_id', false, false);
                $table->integer('meeting_id', false, false);
                $table->integer('workshop_id', false, false);
                $table->timestamp('notes_updated_at')->nullable();
                $table->tinyInteger('is_archived')->default(false);
                $table->timestamps();
            });
            Schema::table('topic_admin_notes', function ($table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('topic_id')->references('id')->on('topics')->onDelete('cascade');
                $table->foreign('meeting_id')->references('id')->on('meetings')->onDelete('cascade');
                $table->foreign('workshop_id')->references('id')->on('workshops')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('topic_admin_notes');
    }
}
