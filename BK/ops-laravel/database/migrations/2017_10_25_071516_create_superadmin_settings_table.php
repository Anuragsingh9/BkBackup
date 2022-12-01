<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSuperadminSettingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('superadmin_settings', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('setting_key', 25);
			$table->text('setting_value', 65535);
			$table->datetime('updated_at')->nullable();
			$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
		});
		$data =[
				["setting_key"=>"graphic_config" , "setting_value"=>'{"headerColor1":{"r":255,"g":255,"b":255,"a":1},"headerColor2":{"r":0,"g":0,"b":0,"a":1},"color1":{"r":10,"g":143,"b":192,"a":1},"color2":{"r":0,"g":106,"b":176,"a":1},"transprancy1":{"a":"0.10","r":84,"g":170,"b":235},"transprancy2":{"a":"0.11","r":35,"g":72,"b":124},"header_logo":"opsimplify-logo.jpg","right_header_icon":"favicon.png"}' ],
			];
		DB::table('superadmin_settings')->insert($data);
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('superadmin_settings');
	}

}
