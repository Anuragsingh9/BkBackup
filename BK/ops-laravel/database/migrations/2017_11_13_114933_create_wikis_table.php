<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWikisTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wikis', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('wiki_name', 100);
			$table->text('wiki_text');
			$table->integer('added_by');
			$table->string('wiki_category_id')->nullable();
			$table->integer('status')->default(1);
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
		Schema::drop('wikis');
	}

}
