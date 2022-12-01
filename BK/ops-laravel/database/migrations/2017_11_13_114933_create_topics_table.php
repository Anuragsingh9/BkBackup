<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTopicsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('topics', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('grand_parent_id')->nullable()->index('FK_topics_topics');
			$table->integer('parent_id')->nullable()->index('FK_topics_topics_2');
			$table->integer('level')->nullable();
			$table->string('topic_title', 500)->nullable();
			$table->integer('meeting_id')->default(0);
			$table->integer('workshop_id')->default(0);
			$table->text('discussion', 65535)->nullable();
			$table->text('decision', 65535)->nullable();
			$table->string('list_order', 50)->nullable();
			$table->boolean('reuse')->default(1);
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
		Schema::drop('topics');
	}

}
