<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRolesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('roles', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('role_key', 50)->default('0');
			$table->string('fr_text', 80)->nullable();
			$table->string('eng_text', 50)->default('0');
			$table->boolean('status')->default(1);
			$table->datetime('updated_at')->nullable();
			$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
		});
		$data =[
			['role_key'=>'M0', 'fr_text'=>'', 'eng_text'=>'Super Admin', 'status'=>0],
			['role_key'=>'M1', 'fr_text'=>'', 'eng_text'=>'Organisation Admin', 'status'=>1],
			['role_key'=>'M2', 'fr_text'=>'', 'eng_text'=>'User', 'status'=>1],
			['role_key'=>'M3', 'fr_text'=>'', 'eng_text'=>'Guest', 'status'=>0],
			['role_key'=>'W0', 'fr_text'=>'', 'eng_text'=>'Workshop Secretary', 'status'=>1],
			['role_key'=>'W1', 'fr_text'=>'', 'eng_text'=>'Workshop Deputy', 'status'=>0],
			['role_key'=>'W2', 'fr_text'=>'', 'eng_text'=>'Workshop Member', 'status'=>0],
			['role_key'=>'K0', 'fr_text'=>'', 'eng_text'=>'Wiki Admin', 'status'=>0],
			['role_key'=>'K1', 'fr_text'=>'', 'eng_text'=>'Wiki Editor', 'status'=>0],
			['role_key'=>'U0', 'fr_text'=>'', 'eng_text'=>'Union Admin', 'status'=>0],
			['role_key'=>'U1', 'fr_text'=>'', 'eng_text'=>'Union Member', 'status'=>0],
			['role_key'=>'C1', 'fr_text'=>'CRM  Administrateur', 'eng_text'=>'CRM Administrator', 'status'=>0],
			['role_key'=>'C2', 'fr_text'=>'CRM Editeur', 'eng_text'=>'CRM Editor', 'status'=>0],
			['role_key'=>'C3', 'fr_text'=>'CRM Finance Team', 'eng_text'=>'CRM Finance Team', 'status'=>0],
			['role_key'=>'C4', 'fr_text'=>'CRM Dev Team', 'eng_text'=>'CRM Dev Team', 'status'=>0],
			['role_key'=>'C5', 'fr_text'=>'CRM Assistance Team', 'eng_text'=>'CRM Assistance Team', 'status'=>0]
			];
		DB::table('roles')->insert($data);
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('roles');
	}

}
