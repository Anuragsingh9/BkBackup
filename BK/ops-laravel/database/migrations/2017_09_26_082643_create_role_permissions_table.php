<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRolePermissionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('role_permissions', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('action_react', 50)->unique('action');
			$table->string('action_laravel', 50);
			$table->enum('action_type', array('GET','POST','PUT','DELETE','PATH'))->default('PATH');
			$table->string('title', 200)->nullable();
			$table->text('description', 65535)->nullable();
			$table->boolean('M0')->default(0);
			$table->boolean('M1')->default(0);
			$table->boolean('M2')->default(0);
			$table->boolean('M3')->default(0);
			$table->boolean('W0')->default(0);
			$table->boolean('W1')->default(0);
			$table->boolean('W2')->default(0);
			$table->boolean('K0')->default(0);
			$table->boolean('K1')->default(0);
			$table->boolean('UO')->default(0);
			$table->boolean('U1')->default(0);
			$table->datetime('updated_at')->nullable();
			$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
		});
		$data=[
			["action_react"=>"addWorkshop" , "action_laravel"=>"add-workshop" , "action_type"=>"POST" , "title"=>"for add workshop" , "description"=>"for add workshop" , "M0"=>"1" , "M1"=>"1" , "M2"=>"0" , "M3"=>"0" , "W0"=>"0" , "W1"=>"0" , "W2"=>"0" , "K0"=>"0" , "K1"=>"0" , "UO"=>"0" , "U1"=>"0"],
["action_react"=>"superAdminSettings" , "action_laravel"=>"super-admin-settings" , "action_type"=>"PATH" , "title"=>"SA Setting page" , "description"=>"Setting in dropdown menu in header" , "M0"=>"1" , "M1"=>"0" , "M2"=>"0" , "M3"=>"0" , "W0"=>"0" , "W1"=>"0" , "W2"=>"0" , "K0"=>"0" , "K1"=>"0" , "UO"=>"0" , "U1"=>"0"],
["action_react"=>"orgAdminSettings" , "action_laravel"=>"org-admin-settings" , "action_type"=>"PATH" , "title"=>"OA Setting Page" , "description"=>"OA Setting in dropdown menu in header" , "M0"=>"1" , "M1"=>"1" , "M2"=>"0" , "M3"=>"0" , "W0"=>"0" , "W1"=>"0" , "W2"=>"0" , "K0"=>"0" , "K1"=>"0" , "UO"=>"0" , "U1"=>"0"],
["action_react"=>"addMember" , "action_laravel"=>"add-member" , "action_type"=>"PATH" , "title"=>"Add Member Page" , "description"=>"Add Member Page" , "M0"=>"1" , "M1"=>"1" , "M2"=>"0" , "M3"=>"0" , "W0"=>"1" , "W1"=>"0" , "W2"=>"0" , "K0"=>"0" , "K1"=>"0" , "UO"=>"0" , "U1"=>"0"],
["action_react"=>"actionMember" , "action_laravel"=>"action-member" , "action_type"=>"PATH" , "title"=>"Action Member" , "description"=>"Action Member" , "M0"=>"1" , "M1"=>"1" , "M2"=>"0" , "M3"=>"0" , "W0"=>"1" , "W1"=>"0" , "W2"=>"0" , "K0"=>"0" , "K1"=>"0" , "UO"=>"0" , "U1"=>"0"],
["action_react"=>"addMeeting" , "action_laravel"=>"add-meeting" , "action_type"=>"POST" , "title"=>"Add Meeting Page" , "description"=>"Add Meeting Page" , "M0"=>"1" , "M1"=>"1" , "M2"=>"0" , "M3"=>"0" , "W0"=>"1" , "W1"=>"0" , "W2"=>"0" , "K0"=>"0" , "K1"=>"0" , "UO"=>"0" , "U1"=>"0"],
["action_react"=>"addPrepd" , "action_laravel"=>"add-prepd" , "action_type"=>"POST" , "title"=>"Add Prepd Page" , "description"=>"Add Prepd Page" , "M0"=>"1" , "M1"=>"1" , "M2"=>"0" , "M3"=>"0" , "W0"=>"1" , "W1"=>"0" , "W2"=>"0" , "K0"=>"0" , "K1"=>"0" , "UO"=>"0" , "U1"=>"0"],
["action_react"=>"addRepd" , "action_laravel"=>"add-repd" , "action_type"=>"POST" , "title"=>"Add Repd Page" , "description"=>"Add Repd Page" , "M0"=>"1" , "M1"=>"1" , "M2"=>"0" , "M3"=>"0" , "W0"=>"1" , "W1"=>"0" , "W2"=>"0" , "K0"=>"0" , "K1"=>"0" , "UO"=>"0" , "U1"=>"0"],
["action_react"=>"editPrepd" , "action_laravel"=>"edit-prepd" , "action_type"=>"POST" , "title"=>"Edit Prepd" , "description"=>"Edit Prepd" , "M0"=>"1" , "M1"=>"1" , "M2"=>"0" , "M3"=>"0" , "W0"=>"1" , "W1"=>"1" , "W2"=>"0" , "K0"=>"0" , "K1"=>"0" , "UO"=>"0" , "U1"=>"0"],
["action_react"=>"editRepd" , "action_laravel"=>"edit-repd" , "action_type"=>"POST" , "title"=>"Edit Repd" , "description"=>"Edit Repd" , "M0"=>"1" , "M1"=>"1" , "M2"=>"0" , "M3"=>"0" , "W0"=>"1" , "W1"=>"1" , "W2"=>"0" , "K0"=>"0" , "K1"=>"0" , "UO"=>"0" , "U1"=>"0"],
["action_react"=>"sendPrepd" , "action_laravel"=>"send-prepd" , "action_type"=>"PATH" , "title"=>"Send Prepd" , "description"=>"Validate a prepd" , "M0"=>"0" , "M1"=>"0" , "M2"=>"0" , "M3"=>"0" , "W0"=>"0" , "W1"=>"1" , "W2"=>"0" , "K0"=>"0" , "K1"=>"0" , "UO"=>"0" , "U1"=>"0"],
["action_react"=>"sendRepd" , "action_laravel"=>"send-repd" , "action_type"=>"PATH" , "title"=>"Send Repd" , "description"=>"Validate a Repd" , "M0"=>"0" , "M1"=>"0" , "M2"=>"0" , "M3"=>"0" , "W0"=>"0" , "W1"=>"1" , "W2"=>"0" , "K0"=>"0" , "K1"=>"0" , "UO"=>"0" , "U1"=>"0"],
["action_react"=>"manageParticipants" , "action_laravel"=>"manage-participants" , "action_type"=>"PATH" , "title"=>"Manage Participants" , "description"=>"Manage Participants" , "M0"=>"1" , "M1"=>"1" , "M2"=>"0" , "M3"=>"0" , "W0"=>"1" , "W1"=>"1" , "W2"=>"0" , "K0"=>"0" , "K1"=>"0" , "UO"=>"0" , "U1"=>"0"],
["action_react"=>"PrepdNotes" , "action_laravel"=>"prepd-notes" , "action_type"=>"PATH" , "title"=>"Can take notes on prepd" , "description"=>"Can take notes on prepd" , "M0"=>"1" , "M1"=>"1" , "M2"=>"0" , "M3"=>"0" , "W0"=>"1" , "W1"=>"1" , "W2"=>"1" , "K0"=>"0" , "K1"=>"0" , "UO"=>"0" , "U1"=>"0"],
["action_react"=>"RepdNotes" , "action_laravel"=>"repd-notes" , "action_type"=>"PATH" , "title"=>"Can take notes on repd" , "description"=>"Can take notes on repd" , "M0"=>"1" , "M1"=>"1" , "M2"=>"0" , "M3"=>"0" , "W0"=>"1" , "W1"=>"1" , "W2"=>"1" , "K0"=>"0" , "K1"=>"0" , "UO"=>"0" , "U1"=>"0"],
["action_react"=>"addMessage" , "action_laravel"=>"add-message" , "action_type"=>"POST" , "title"=>"add message" , "description"=>"add message" , "M0"=>"1" , "M1"=>"1" , "M2"=>"0" , "M3"=>"0" , "W0"=>"1" , "W1"=>"0" , "W2"=>"1" , "K0"=>"0" , "K1"=>"0" , "UO"=>"0" , "U1"=>"0"],
["action_react"=>"addDocument" , "action_laravel"=>"add-document" , "action_type"=>"POST" , "title"=>"add document" , "description"=>"add document" , "M0"=>"1" , "M1"=>"1" , "M2"=>"0" , "M3"=>"0" , "W0"=>"1" , "W1"=>"0" , "W2"=>"0" , "K0"=>"0" , "K1"=>"0" , "UO"=>"0" , "U1"=>"0"],
["action_react"=>"searchDocument" , "action_laravel"=>"search-document" , "action_type"=>"POST" , "title"=>"search document" , "description"=>"search document" , "M0"=>"1" , "M1"=>"1" , "M2"=>"0" , "M3"=>"0" , "W0"=>"1" , "W1"=>"0" , "W2"=>"1" , "K0"=>"0" , "K1"=>"0" , "UO"=>"0" , "U1"=>"0"],
["action_react"=>"addWiki" , "action_laravel"=>"wiki" , "action_type"=>"POST" , "title"=>"add wiki" , "description"=>"add wiki" , "M0"=>"1" , "M1"=>"1" , "M2"=>"0" , "M3"=>"0" , "W0"=>"0" , "W1"=>"0" , "W2"=>"0" , "K0"=>"1" , "K1"=>"0" , "UO"=>"0" , "U1"=>"0"],
["action_react"=>"inviteEditor" , "action_laravel"=>"invite-editor" , "action_type"=>"POST" , "title"=>"invite editor" , "description"=>"invite editor" , "M0"=>"1" , "M1"=>"1" , "M2"=>"0" , "M3"=>"0" , "W0"=>"0" , "W1"=>"0" , "W2"=>"0" , "K0"=>"1" , "K1"=>"0" , "UO"=>"0" , "U1"=>"0"],
["action_react"=>"editWiki" , "action_laravel"=>"wiki" , "action_type"=>"POST" , "title"=>"Edit wiki" , "description"=>"Edit wiki" , "M0"=>"1" , "M1"=>"1" , "M2"=>"0" , "M3"=>"0" , "W0"=>"0" , "W1"=>"0" , "W2"=>"0" , "K0"=>"1" , "K1"=>"1" , "UO"=>"0" , "U1"=>"0"],
["action_react"=>"getUnion" , "action_laravel"=>"get-union" , "action_type"=>"POST" , "title"=>"Get Union" , "description"=>"Get Union" , "M0"=>"1" , "M1"=>"1" , "M2"=>"0" , "M3"=>"0" , "W0"=>"0" , "W1"=>"0" , "W2"=>"0" , "K0"=>"0" , "K1"=>"0" , "UO"=>"1" , "U1"=>"1"],
["action_react"=>"viewPrepd" , "action_laravel"=>"view-prepd" , "action_type"=>"POST" , "title"=>"View Prepd" , "description"=>"View Prepd" , "M0"=>"1" , "M1"=>"1" , "M2"=>"0" , "M3"=>"1" , "W0"=>"1" , "W1"=>"1" , "W2"=>"1" , "K0"=>"0" , "K1"=>"0" , "UO"=>"0" , "U1"=>"0"],
["action_react"=>"actionMeeting" , "action_laravel"=>"action-meeting" , "action_type"=>"PATH" , "title"=>"Action Meeting" , "description"=>"Action Meeting" , "M0"=>"1" , "M1"=>"1" , "M2"=>"0" , "M3"=>"0" , "W0"=>"1" , "W1"=>"0" , "W2"=>"0" , "K0"=>"0" , "K1"=>"0" , "UO"=>"0" , "U1"=>"0"],
["action_react"=>"editUnion" , "action_laravel"=>"edit-union" , "action_type"=>"POST" , "title"=>"Edit Union" , "description"=>"Edit Union" , "M0"=>"1" , "M1"=>"1" , "M2"=>"0" , "M3"=>"0" , "W0"=>"0" , "W1"=>"0" , "W2"=>"0" , "K0"=>"0" , "K1"=>"0" , "UO"=>"1" , "U1"=>"0"],
["action_react"=>"threeSixty" , "action_laravel"=>"three-sixty" , "action_type"=>"POST" , "title"=>"Three Sixty" , "description"=>"Three Sixty" , "M0"=>"1" , "M1"=>"1" , "M2"=>"0" , "M3"=>"0" , "W0"=>"0" , "W1"=>"0" , "W2"=>"0" , "K0"=>"0" , "K1"=>"0" , "UO"=>"0" , "U1"=>"0"],
["action_react"=>"commission" , "action_laravel"=>"commission" , "action_type"=>"POST" , "title"=>"commission page" , "description"=>"piloter commission page" , "M0"=>"1" , "M1"=>"1" , "M2"=>"0" , "M3"=>"0" , "W0"=>"1" , "W1"=>"1" , "W2"=>"0" , "K0"=>"0" , "K1"=>"0" , "UO"=>"0" , "U1"=>"0"],
["action_react"=>"people" , "action_laravel"=>"people" , "action_type"=>"POST" , "title"=>"People" , "description"=>"People" , "M0"=>"1" , "M1"=>"1" , "M2"=>"0" , "M3"=>"0" , "W0"=>"0" , "W1"=>"0" , "W2"=>"0" , "K0"=>"0" , "K1"=>"0" , "UO"=>"0" , "U1"=>"0"],
["action_react"=>"document" , "action_laravel"=>"document" , "action_type"=>"POST" , "title"=>"document page" , "description"=>"document page" , "M0"=>"1" , "M1"=>"1" , "M2"=>"0" , "M3"=>"0" , "W0"=>"1" , "W1"=>"1" , "W2"=>"0" , "K0"=>"0" , "K1"=>"0" , "UO"=>"0" , "U1"=>"0"],
["action_react"=>"website" , "action_laravel"=>"website" , "action_type"=>"POST" , "title"=>"Website" , "description"=>"Website" , "M0"=>"1" , "M1"=>"1" , "M2"=>"0" , "M3"=>"0" , "W0"=>"0" , "W1"=>"0" , "W2"=>"0" , "K0"=>"0" , "K1"=>"0" , "UO"=>"0" , "U1"=>"0"],
["action_react"=>"StartPage" , "action_laravel"=>"start-page" , "action_type"=>"PATH" , "title"=>"Start Page" , "description"=>"Start Page" , "M0"=>"1" , "M1"=>"1" , "M2"=>"0" , "M3"=>"0" , "W0"=>"0" , "W1"=>"0" , "W2"=>"0" , "K0"=>"0" , "K1"=>"0" , "UO"=>"0" , "U1"=>"0"],
["action_react"=>"organiseCommission" , "action_laravel"=>"organise commission" , "action_type"=>"POST" , "title"=>"organise commission page" , "description"=>"organise piloter commission page" , "M0"=>"1" , "M1"=>"1" , "M2"=>"0" , "M3"=>"0" , "W0"=>"1" , "W1"=>"1" , "W2"=>"1" , "K0"=>"0" , "K1"=>"0" , "UO"=>"0" , "U1"=>"0"]
		];
		DB::table('role_permissions')->insert($data);
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('role_permissions');
	}

}
