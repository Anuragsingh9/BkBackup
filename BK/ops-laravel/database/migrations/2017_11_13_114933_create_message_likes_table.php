<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMessageLikesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('message_likes', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('workshop_id')->nullable();
			$table->integer('message_id')->nullable();
			$table->integer('message_reply_id')->nullable();
			$table->integer('user_id')->nullable();
			$table->integer('status')->default(0)->comment('1=like 0=unlike');
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
		Schema::drop('message_likes');
	}

}
