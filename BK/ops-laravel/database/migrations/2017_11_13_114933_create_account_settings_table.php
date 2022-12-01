<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAccountSettingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('account_settings', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('account_id');
			$table->integer('test_version')->default(0);
			$table->dateTime('date_from');
			$table->dateTime('date_to');
			$table->integer('light_version')->default(0)->comment('Light restricted version');
			$table->integer('mobile_enable');
			$table->integer('wvm_enable')->default(0)->comment('Workshop Video Meetings enable');
			$table->integer('fvm_enable')->default(0)->comment('Flash Video Meetings enable');
			$table->integer('user_group_enable')->default(0);
			$table->integer('wiki_enable')->default(0);
			$table->integer('reminder_enable')->default(0);
			$table->integer('zip_download')->nullable()->default(0);
			$table->integer('fts_enable')->default(0)->comment('Full Text seach enabled');
			$table->integer('repd_connect_mode')->default(0)->comment('REPD Not Connected enabled mode');
			$table->integer('prepd_repd_notes')->default(0)->comment('PREDP / REPD notes enabled');
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
		Schema::drop('account_settings');
	}

}
