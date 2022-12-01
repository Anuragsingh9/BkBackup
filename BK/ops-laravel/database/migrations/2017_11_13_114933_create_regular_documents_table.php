<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRegularDocumentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('regular_documents', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('workshop_id');
			$table->integer('event_id')->nullable();
			$table->integer('message_category_id')->nullable();
			$table->integer('user_id')->nullable();
			$table->integer('created_by_user_id');
			$table->string('document_title');
			$table->integer('document_type_id')->nullable();
			$table->text('document_file', 65535);
			$table->integer('issuer_id')->nullable();
			$table->integer('is_active')->default(1);
			$table->integer('increment_number')->unsigned()->nullable();
			$table->integer('download_count')->default(0);
			$table->integer('uncote')->default(0)->comment('0=cote, 1= uncote');
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
		Schema::drop('regular_documents');
	}

}
