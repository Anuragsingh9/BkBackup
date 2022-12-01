<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTopicNotesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('topic_notes', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('meeting_id')->nullable();
			$table->integer('topic_id')->nullable();
			$table->integer('user_id')->nullable();
			$table->text('topic_note', 65535)->nullable();
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
		Schema::drop('topic_notes');
	}

}
