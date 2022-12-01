<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrganisationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('organisation', function(Blueprint $table)
		{
			$table->charset='utf8mb4';
            $table->collation='utf8mb4_unicode_ci';
			$table->integer('id', true);
			$table->integer('account_id')->nullable()->comment('hostnames table id');
			$table->string('fname', 100)->nullable();
			$table->string('lname', 100)->nullable();
			$table->string('email', 100)->nullable();
			$table->string('password', 100)->nullable();
			$table->string('name_org', 100)->nullable();
			$table->string('members_count', 20)->nullable();
			$table->string('acronym', 20)->nullable();
			$table->string('sector', 20)->nullable();
			$table->string('permanent_member', 20)->nullable();
			$table->string('logo', 200)->nullable();
			$table->string('icon', 200)->nullable();
			$table->string('bashlinelogo', 200)->nullable();
			$table->text('address1', 65535)->charset('utf8mb4')->collate('utf8mb4_unicode_ci')->nullable();
			$table->text('address2', 65535)->nullable();
			$table->string('postal_code', 50)->nullable();
			$table->string('city', 100)->nullable();
			$table->string('country', 100)->default('France');
			$table->integer('commissions')->nullable();
			$table->integer('working_groups')->nullable();
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
		Schema::drop('organisation');
	}

}
