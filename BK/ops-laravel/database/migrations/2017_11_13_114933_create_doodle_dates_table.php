<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDoodleDatesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('doodle_dates', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('meeting_id');
			$table->date('date')->nullable();
			$table->string('start_time', 20)->nullable();
			$table->string('end_time', 20)->nullable();
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
		Schema::drop('doodle_dates');
	}

}
