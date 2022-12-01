<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWorkshopMetasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('workshop_metas', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('workshop_id');
			$table->integer('user_id')->unsigned()->index('workshop_user_relation');
			$table->boolean('role')->default(0)->comment('0=member,1=president,2=validator');
			$table->integer('meeting_id')->nullable();;
			$table->datetime('updated_at')->nullable();
        $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->index(['workshop_id','user_id'], 'workshop_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('workshop_metas');
	}

}
