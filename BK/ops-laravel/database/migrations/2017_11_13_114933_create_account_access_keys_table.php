<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAccountAccessKeysTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('account_access_keys', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('fqdn_id')->nullable();
			$table->string('fqdn_url')->nullable();
			$table->string('access_token')->nullable();
			$table->string('ip')->nullable();
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
		Schema::drop('account_access_keys');
	}

}
