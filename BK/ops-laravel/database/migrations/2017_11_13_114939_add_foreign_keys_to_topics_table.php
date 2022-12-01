<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToTopicsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('topics', function(Blueprint $table)
		{
			$table->foreign('grand_parent_id', 'FK_topics_topics')->references('id')->on('topics')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('parent_id', 'FK_topics_topics_2')->references('id')->on('topics')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('topics', function(Blueprint $table)
		{
			$table->dropForeign('FK_topics_topics');
			$table->dropForeign('FK_topics_topics_2');
		});
	}

}
