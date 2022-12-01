<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateIssuersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('issuers', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('issuer_name');
			$table->string('issuer_code', 250)->nullable();
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
		Schema::drop('issuers');
	}

}