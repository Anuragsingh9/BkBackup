<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToMessageRepliesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('message_replies', function(Blueprint $table)
		{
			$table->foreign('message_id', 'message_constraint')->references('id')->on('messages')->onUpdate('NO ACTION')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('message_replies', function(Blueprint $table)
		{
			$table->dropForeign('message_constraint');
		});
	}

}
