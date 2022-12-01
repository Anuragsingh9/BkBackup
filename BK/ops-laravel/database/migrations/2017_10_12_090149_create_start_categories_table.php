<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStartCategoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('start_categories', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('title_fr', 200);
			$table->string('title_en')->nullable();
			$table->integer('status')->default(1)->comment('0=Deactive, 1=Active');
			$table->integer('sort_order')->default(0);
			$table->datetime('updated_at')->nullable();
			$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
		});
		$data =[
				["title_fr"=>"Configurez votre simplify" , "title_en"=>"Configure your simplify" , "status"=>"1" , "sort_order"=>"1"],
["title_fr"=>"Administrer les droits d'accès" , "title_en"=>"Administer access rights" , "status"=>"1" , "sort_order"=>"2"],
["title_fr"=>"Cotez votre premier document" , "title_en"=>"Rate your first document" , "status"=>"1" , "sort_order"=>"5"],
["title_fr"=>"Créez votre première commission" , "title_en"=>"Create your first commission" , "status"=>"1" , "sort_order"=>"4"],
["title_fr"=>"Créez votre premier document en mode partagé" , "title_en"=>"Create your first document in shared mode" , "status"=>"1" , "sort_order"=>"6"],
["title_fr"=>"Créez votre première réunion de commission" , "title_en"=>"Create your first commission meeting" , "status"=>"1" , "sort_order"=>"7"],
["title_fr"=>"Gérez votre première réunion à distance" , "title_en"=>"Manage your first remote meeting" , "status"=>"1" , "sort_order"=>"8"],
["title_fr"=>"Configurez votre écosystème" , "title_en"=>"Set up you ecosystem" , "status"=>"1" , "sort_order"=>"3"],
			];
		DB::table('start_categories')->insert($data);
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('start_categories');
	}

}
