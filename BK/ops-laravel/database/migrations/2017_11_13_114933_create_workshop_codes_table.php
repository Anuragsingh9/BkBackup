<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWorkshopCodesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('workshop_codes', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('workshop_id')->nullable();
			$table->string('code')->nullable();
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
		Schema::drop('workshop_codes');
	}

}
