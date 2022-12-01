<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUnionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('unions', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('union_name', 50);
			$table->string('union_code', 10);
			$table->string('logo', 100)->nullable();
			$table->text('union_description', 65535)->nullable();
			$table->integer('family_id');
			$table->integer('industry_id');
			$table->string('address1');
			$table->string('address2')->nullable();
			$table->string('postal_code', 10);
			$table->string('city', 50);
			$table->string('country', 50);
			$table->string('telephone', 20)->nullable();
			$table->string('fax', 20)->nullable();
			$table->string('email', 50);
			$table->string('website', 50);
			$table->string('contact_button', 20);
			$table->boolean('union_type')->default(0);
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
		Schema::drop('unions');
	}

}
