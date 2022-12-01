<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWorkshopsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('workshops', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('president_id')->nullable()->comment('user id');
			$table->integer('validator_id')->nullable()->comment('user id');
			$table->text('workshop_name', 65535);
			$table->text('workshop_desc', 65535)->nullable();
			$table->string('code1', 50);
			$table->string('code2', 50)->nullable();
			$table->string('workshop_type', 50)->default('Commission')->comment('Commission or Work Group');
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
		Schema::drop('workshops');
	}

}
