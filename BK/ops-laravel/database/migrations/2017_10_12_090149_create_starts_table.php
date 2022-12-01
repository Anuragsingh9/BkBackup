<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStartsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('starts', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('start_category_id');
			$table->string('title_fr', 200)->nullable();
			$table->string('title_en')->nullable();
			$table->string('url', 200);
			$table->integer('status')->default(1)->comment('0=Deactive, 1=Active');
			$table->integer('sort_order')->default(0);
			$table->datetime('updated_at')->nullable();
			$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
		});
		$data =[
				["id"=>"1" , "start_category_id"=>"1" , "title_fr"=>"Configurez vos groupes d'utilisateurs" , "title_en"=>"Configure your user groups" , "url"=>"settings/user-groups/add-list" , "status"=>"1" , "sort_order"=>"1"],
["id"=>"2" , "start_category_id"=>"1" , "title_fr"=>"Configurer la génération des PDFs" , "title_en"=>"Configure PDF generation" , "url"=>"settings/pdf-graphics" , "status"=>"1" , "sort_order"=>"3"],
["id"=>"3" , "start_category_id"=>"1" , "title_fr"=>"Configurer graphiquement l'application" , "title_en"=>"Configure the application graphically" , "url"=>"settings/super-admin-settings/graphic-config" , "status"=>"1" , "sort_order"=>"2"],
["id"=>"4" , "start_category_id"=>"1" , "title_fr"=>"Configurer l'envoi des emails" , "title_en"=>"Configure sending emails" , "url"=>"settings/email-graphics" , "status"=>"1" , "sort_order"=>"4"],
["id"=>"5" , "start_category_id"=>"1" , "title_fr"=>"Structurez votre base documentaire" , "title_en"=>"Structure your document base" , "url"=>"settings/documents/add-list-doc-type" , "status"=>"1" , "sort_order"=>"5"],
["id"=>"6" , "start_category_id"=>"1" , "title_fr"=>"Structurez votre médiathèque" , "title_en"=>"Structure your media library" , "url"=>"settings/resources/add-catagory" , "status"=>"1" , "sort_order"=>"7"],
["id"=>"7" , "start_category_id"=>"2" , "title_fr"=>"Créez vos administrateurs" , "title_en"=>"Create Your Administrators" , "url"=>"organiser/users/add-user" , "status"=>"1" , "sort_order"=>"1"],
["id"=>"8" , "start_category_id"=>"2" , "title_fr"=>"Gérez vos responsables de commissions et de GTs" , "title_en"=>"Manage your commission and GTs managers" , "url"=>"settings/commissions/list" , "status"=>"1" , "sort_order"=>"2"],
["id"=>"9" , "start_category_id"=>"2" , "title_fr"=>"Gérez vos responsables de wikis" , "title_en"=>"Manage your wikis managers" , "url"=>"wikis/list" , "status"=>"1" , "sort_order"=>"3"],
["id"=>"10" , "start_category_id"=>"2" , "title_fr"=>"Gérez vos responsables adhérents" , "title_en"=>"Manage your member managers" , "url"=>"start" , "status"=>"1" , "sort_order"=>"4"],
["id"=>"11" , "start_category_id"=>"2" , "title_fr"=>"Gérez vos responsables assistance aux adhérents" , "title_en"=>"Manage your member support managers" , "url"=>"start" , "status"=>"1" , "sort_order"=>"5"],
["id"=>"17" , "start_category_id"=>"3" , "title_fr"=>"Vérifiez les codes de cotation de votre commission" , "title_en"=>"Check the rating codes of your commission" , "url"=>"organiser/commissions/add" , "status"=>"1" , "sort_order"=>"2"],
["id"=>"18" , "start_category_id"=>"3" , "title_fr"=>"Cotez votre premier document" , "title_en"=>"Rate your first document" , "url"=>"organiser/documents/add-document" , "status"=>"1" , "sort_order"=>"3"],
["id"=>"19" , "start_category_id"=>"3" , "title_fr"=>"Faire votre première recherche de documents" , "title_en"=>"Make your first search for documents" , "url"=>"organiser/documents/list" , "status"=>"1" , "sort_order"=>"4"],
["id"=>"20" , "start_category_id"=>"4" , "title_fr"=>"Créez votre votre première commission" , "title_en"=>"Create your first committee" , "url"=>"organiser/commissions/add" , "status"=>"1" , "sort_order"=>"1"],
["id"=>"21" , "start_category_id"=>"4" , "title_fr"=>"Créez un membre de test pour votre commission" , "title_en"=>"Create a test member for your commission" , "url"=>"organiser/commissions/1/member/add-member" , "status"=>"1" , "sort_order"=>"2"],
["id"=>"23" , "start_category_id"=>"5" , "title_fr"=>"Créez le document à partager" , "title_en"=>"Create the document to share" , "url"=>"wikis/add" , "status"=>"1" , "sort_order"=>"1"],
["id"=>"24" , "start_category_id"=>"5" , "title_fr"=>"Invitez les éditeurs autorisés" , "title_en"=>"Invite Authorized Publishers" , "url"=>"start-redirect/invite-editor" , "status"=>"1" , "sort_order"=>"2"],
["id"=>"25" , "start_category_id"=>"5" , "title_fr"=>"Validez le document final" , "title_en"=>"Validate the final document" , "url"=>"wikis/add" , "status"=>"1" , "sort_order"=>"3"],
["id"=>"26" , "start_category_id"=>"6" , "title_fr"=>"Créer une première réunion à date fixe" , "title_en"=>"Create a first fixed date meeting" , "url"=>"organiser/commissions/1/meeting/add" , "status"=>"1" , "sort_order"=>"1"],
["id"=>"27" , "start_category_id"=>"6" , "title_fr"=>"ou Proposez plusieurs dates pour votre réunion" , "title_en"=>"or Propose several dates for your meeting" , "url"=>"organiser/commissions/1/meeting/add" , "status"=>"1" , "sort_order"=>"2"],
["id"=>"28" , "start_category_id"=>"6" , "title_fr"=>"Rédigez votre premier ordre du jour" , "title_en"=>"Write your first agenda" , "url"=>"start-redirect/agenda" , "status"=>"1" , "sort_order"=>"3"],
["id"=>"29" , "start_category_id"=>"6" , "title_fr"=>"Préparez vos notes pour la réunion" , "title_en"=>"Prepare your notes for the meeting" , "url"=>"start-redirect/agenda" , "status"=>"1" , "sort_order"=>"4"],
["id"=>"30" , "start_category_id"=>"6" , "title_fr"=>"Vérifiez la liste des invités à votre réunion" , "title_en"=>"Check the list of guests at your meeting" , "url"=>"organiser/commissions/1/members" , "status"=>"1" , "sort_order"=>"5"],
["id"=>"31" , "start_category_id"=>"6" , "title_fr"=>"Envoyez votre premier ordre du jour" , "title_en"=>"Send your first agenda" , "url"=>"start-redirect/agenda" , "status"=>"1" , "sort_order"=>"6"],
["id"=>"32" , "start_category_id"=>"6" , "title_fr"=>"Vérifiez la liste des inscrits" , "title_en"=>"Check the list of registrants" , "url"=>"start-redirect/inscription" , "status"=>"1" , "sort_order"=>"7"],
["id"=>"33" , "start_category_id"=>"6" , "title_fr"=>"Rédigez votre premier relevé de décisions" , "title_en"=>"Write your first statement of decisions" , "url"=>"start-redirect/repd" , "status"=>"1" , "sort_order"=>"8"],
["id"=>"34" , "start_category_id"=>"6" , "title_fr"=>"Envoyez votre premier relevé de décisions" , "title_en"=>"Send your first statement of decisions" , "url"=>"start-redirect/repd" , "status"=>"1" , "sort_order"=>"9"],
["id"=>"35" , "start_category_id"=>"7" , "title_fr"=>"Définissez votre présentateur" , "title_en"=>"Define your presenter" , "url"=>"start" , "status"=>"1" , "sort_order"=>"1"],
["id"=>"36" , "start_category_id"=>"7" , "title_fr"=>"Préparez les documents dont vous aurez besoin" , "title_en"=>"Prepare the documents you will need" , "url"=>"start" , "status"=>"1" , "sort_order"=>"2"],
["id"=>"37" , "start_category_id"=>"7" , "title_fr"=>"Révisez l'utilisation de la télécommande" , "title_en"=>"Review the use of the remote control" , "url"=>"start" , "status"=>"1" , "sort_order"=>"3"],
["id"=>"38" , "start_category_id"=>"7" , "title_fr"=>"Révisez les règles pour une réunion à distance efficace" , "title_en"=>"Review the rules for an effective remote meeting" , "url"=>"start" , "status"=>"1" , "sort_order"=>"4"],
["id"=>"39" , "start_category_id"=>"7" , "title_fr"=>"Démarrez votre réunion à distance" , "title_en"=>"Start your meeting remotely" , "url"=>"start" , "status"=>"1" , "sort_order"=>"5"],
["id"=>"40" , "start_category_id"=>"8" , "title_fr"=>"Créez vos familles d’industries" , "title_en"=>"Create your industry families" , "url"=>"settings/industries/add-family" , "status"=>"1" , "sort_order"=>"1"],
["id"=>"41" , "start_category_id"=>"8" , "title_fr"=>"Créez vos industries" , "title_en"=>"Create your industries" , "url"=>"settings/industries/add-industries" , "status"=>"1" , "sort_order"=>"2"],
["id"=>"42" , "start_category_id"=>"1" , "title_fr"=>"Structurez votre émetteurs de documents" , "title_en"=>"Structure your document issuers " , "url"=>"settings/documents/add-list-issuer" , "status"=>"1" , "sort_order"=>"6"]
			];
		DB::table('starts')->insert($data);
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('starts');
	}

}
