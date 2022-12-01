<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTimelinesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('timelines', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('workshop_id', 40);
			$table->string('type', 100)->nullable()->comment('deleted/milestone/ file/invoice/file-message/project-message/milestone/task/project/bug/payment');
			$table->string('description', 100)->nullable();
			$table->string('action', 100)->nullable();
			$table->integer('user_id')->nullable();
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
		Schema::drop('timelines');
	}

}
