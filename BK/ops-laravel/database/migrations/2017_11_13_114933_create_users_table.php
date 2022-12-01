<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('fname', 80);
			$table->string('lname', 80);
			$table->string('email', 80);
			$table->string('password', 100);
			$table->string('phone', 12)->nullable();
			$table->string('mobile', 12)->nullable();
			$table->text('address', 65535)->nullable();
			$table->string('postal', 8)->nullable();
			$table->string('city', 80)->nullable();
			$table->string('country', 80)->nullable();
			$table->string('role', 5)->nullable();
			$table->boolean('role_commision')->nullable();
			$table->boolean('role_wiki')->nullable();
			$table->text('function_union', 65535)->nullable();
			$table->text('society', 65535)->nullable();
			$table->text('function_society', 65535)->nullable();
			$table->integer('family_id')->nullable();
			$table->integer('industry_id')->nullable();
			$table->integer('union_id')->nullable();
			$table->text('avatar', 65535)->nullable();
			$table->string('remember_token', 100)->nullable();
			$table->string('identifier', 100)->nullable();
			$table->integer('login_count')->default(0)->comment('How many times user login');
			$table->string('login_code',20)->nullable();
            $table->string('hash_code',20)->nullable();
            $table->tinyInteger('on_off')->default(1);
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
		Schema::drop('users');
	}

}
