<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTopicDocumentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('topic_documents', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('topic_id')->index('topic_id');
			$table->integer('document_id')->index('FK_topic_documents_regular_documents');
			$table->integer('created_by_user_id');
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
		Schema::drop('topic_documents');
	}

}
