<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateResourcesCategoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('resources_categories', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('category_name', 50);
			$table->text('category_desc', 65535);
			$table->integer('parent')->nullable();
			$table->string('resources_type', 50);
			$table->string('group_id')->nullable();
			$table->integer('is_public')->default(0);
			$table->integer('is_private')->default(0);
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
		Schema::drop('resources_categories');
	}

}
