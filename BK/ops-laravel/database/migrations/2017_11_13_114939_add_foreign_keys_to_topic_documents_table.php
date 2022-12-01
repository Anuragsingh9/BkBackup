<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToTopicDocumentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('topic_documents', function(Blueprint $table)
		{
			$table->foreign('document_id', 'FK_topic_documents_regular_documents')->references('id')->on('regular_documents')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('topic_id', 'FK_topic_documents_topics')->references('id')->on('topics')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('topic_documents', function(Blueprint $table)
		{
			$table->dropForeign('FK_topic_documents_regular_documents');
			$table->dropForeign('FK_topic_documents_topics');
		});
	}

}
