<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToWorkshopMetasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('workshop_metas', function(Blueprint $table)
		{
			$table->foreign('workshop_id', 'workshop_meta_relation')->references('id')->on('workshops')->onUpdate('NO ACTION')->onDelete('CASCADE');
			$table->foreign('meeting_id', 'meeting_meta_relation')->references('id')->on('meetings')->onUpdate('NO ACTION')->onDelete('CASCADE');
			$table->foreign('user_id', 'workshop_user_relation')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('workshop_metas', function(Blueprint $table)
		{
			$table->dropForeign('workshop_meta_relation');
			$table->dropForeign('workshop_user_relation');
		});
	}

}
