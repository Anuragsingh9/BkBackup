<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMeetingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('meetings', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->text('name', 65535)->nullable();
			$table->string('code', 50)->nullable();
			$table->text('description', 65535)->nullable();
			$table->text('place', 65535)->nullable()->comment('from google map autocomplete');
			$table->string('mail', 50)->nullable();
			$table->string('contact_no', 50)->nullable();
			$table->string('image', 80)->nullable();
			$table->string('header_image', 80)->nullable();
			$table->string('lat', 50)->nullable();
			$table->string('long', 50)->nullable();
			$table->date('date')->nullable();
			$table->time('start_time')->nullable();
			$table->time('end_time')->nullable();
			$table->integer('meeting_date_type')->default(1)->comment('0=Multiple 1=Single');
			$table->enum('meeting_type', array('1','2','3'))->comment('1=physical meeting, 2= remote meeting , 3= hybrid meeting');
			$table->integer('workshop_id');
			$table->integer('user_id');
			$table->integer('visibility')->default(0);
			$table->integer('status')->default(1)->comment('1=active,0=disabled');
			$table->dateTime('prepd_published_on')->nullable();
			$table->dateTime('repd_published_on')->nullable();
			$table->integer('prepd_published_by_user_id')->nullable();
			$table->integer('repd_published_by_user_id')->nullable();
			$table->integer('validated_prepd')->default(0);
			$table->integer('validated_repd')->default(0);
			$table->string('redacteur', 100)->nullable();
			$table->integer('is_offline')->default(0);
			$table->boolean('is_downloaded')->default(0);
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
		Schema::drop('meetings');
	}

}
