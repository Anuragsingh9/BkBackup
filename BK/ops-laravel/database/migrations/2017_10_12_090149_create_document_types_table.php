<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDocumentTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('document_types', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('document_name');
			$table->string('document_code');
			$table->datetime('updated_at')->nullable();
			$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
		});
		$data =[
				["document_name"=>"Document de travail" , "document_code"=>"TRAV"],
				["document_name"=>"Ordre du jour" , "document_code"=>"PREPD"],
				["document_name"=>"Relevé de décisions" , "document_code"=>"REPD"]	
			];
		DB::table('document_types')->insert($data);
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('document_types');
	}

}
