<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUnionContactsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('union_contacts', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('union_id');
			$table->string('f_name', 50);
			$table->string('l_name', 20);
			$table->string('position', 35);
			$table->integer('display')->nullable()->default(0);
			$table->string('photo', 100)->nullable();
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
		Schema::drop('union_contacts');
	}

}
