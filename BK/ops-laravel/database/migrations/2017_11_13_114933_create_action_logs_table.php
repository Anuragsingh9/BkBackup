<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActionLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('action_logs', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('menu', 100)->nullable();
			$table->string('sub_menu', 100)->nullable();
			$table->string('action', 100);
			$table->integer('user_id');
			$table->string('ip_address', 50);
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
		Schema::drop('action_logs');
	}

}
