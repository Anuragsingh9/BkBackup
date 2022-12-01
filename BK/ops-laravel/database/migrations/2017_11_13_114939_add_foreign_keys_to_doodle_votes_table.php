<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToDoodleVotesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('doodle_votes', function(Blueprint $table)
		{
			$table->foreign('doodle_id', 'doodle_votes_ibfk_1')->references('id')->on('doodle_dates')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('doodle_votes', function(Blueprint $table)
		{
			$table->dropForeign('doodle_votes_ibfk_1');
		});
	}

}
