<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePresencesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('presences', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('workshop_id');
			$table->integer('meeting_id');
			$table->integer('user_id');
			$table->string('register_status', 10);
			$table->string('presence_status', 10);
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
		Schema::drop('presences');
	}

}
