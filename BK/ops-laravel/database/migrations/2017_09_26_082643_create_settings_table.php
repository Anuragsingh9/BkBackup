<?php

header('Content-Type: text/html; charset=utf-8');

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSettingsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('settings', function(Blueprint $table) {
            $table->integer('id', true);
            $table->string('setting_key', 100);
            $table->text('setting_value', 65535);
            $table->datetime('updated_at')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
        $data = [
            [
                "setting_key" => "pdf_graphic",
                "setting_value" => '{"color1":{"r":104,"g":201,"b":35,"a":1},"color2":{"r":221,"g":92,"b":147,"a":1},"footer_line1":"","footer_line2":"","header_logo":""}'
            ],
            [
                "setting_key" => "platform_graphic",
                "setting_value" => '{"icon":""}'
            ],
                [
                "setting_key" => "email_graphic",
                "setting_value" => '{"email_sign":"Dan2 Ake ADN\r\n\r\ndanakedirect@gmail.com\r\n\r\nTel : 01 42 78 00 00","top_banner":"header-img.jpg","bottom_banner":"footer-img.jpg"}'
            ],
                [
                "setting_key" => "push_notification_graphic",
                "setting_value" => '{"icon":""}'
                ],

              [
                    "setting_key"=>"msg_email_setting" , 
                    "setting_value"=>'{"email_subject":"Vous avez reçu un message","text_before_link":"<p>[[UserFirstName]]<br />\n<br />\nVous avez re&ccedil;u un message.<br />\nDe: [[WorkshopLongName]]&nbsp; [[MessageCategory]]<br />\n<br />\nPour r&eacute;pondre &agrave; ce message,</p>\n","text_after_link":""}'
              ],
                  [
                        "setting_key"=>"personal_email_setting" , 
                        "setting_value"=>'{"email_subject":"Vous avez reçu un message","text_before_link":"<p>[[UserFirstName]]<br />\n<br />\nVous avez re&ccedil;u un message.<br />\nDe: [[WorkshopLongName]]&nbsp; [[MessageCategory]]<br />\n<br />\nPour r&eacute;pondre &agrave; ce message,</p>\n","text_after_link":""}'
                  ],
                [
                    "setting_key"=>"agenda_email_setting" , 
                    "setting_value"=>'{"email_subject":"Ordre du jour de la réunion [[WorkshopLongName]]  [[WorkshopMeetingName]] du [[WorkshopMeetingDate]]  à [[WorkshopMeetingTime]] ","text_before_link":"<p>Bonjour,<br />\n<br />\nVeuillez trouver ci-joint l&rsquo;ordre du jour de la r&eacute;union&nbsp; du&nbsp; [[WorkshopMeetingDate]] &agrave; [[WorkshopMeetingTime]], incluant les documents de travail.<br />\n&nbsp;</p>\n","text_after_link":"<p><br />\n<br />\n[[WorkshopPresidentFullName]]<br />\n[[PresidentEmail]]<br />\n[[PresidentPhone]]<br />\n<br />\n&nbsp;</p>\n"}'
                ],
                [
                    "setting_key"=>"decision_email_setting" , 
                    "setting_value"=>'{"email_subject":"Relevé de décisions de la réunion [[WorkshopLongName]] [[WorkshopMeetingName]] du [[WorkshopMeetingDate]] à [[WorkshopMeetingTime]]","text_before_link":"<p>Bonjour,<br />\n<br />\nVeuillez trouver ci-joint le relev&eacute; de d&eacute;cisions de la r&eacute;union&nbsp; [[WorkshopLongName]] du&nbsp; [[WorkshopMeetingDate]] &agrave; [[WorkshopMeetingTime]].<br />\n&nbsp;</p>\n","text_after_link":"<p><br /><br />[[WorkshopPresidentFullName]]<br />\n[[PresidentEmail]]<br />\n[[PresidentPhone]]<br />\n&nbsp;</p>\n"}'
                ],
                
                [
                    "setting_key"=>"doodle_email_setting" , 
                    "setting_value"=>'{"email_subject":"Choix des dates pour la prochaine réunion [[WorkshopLongName]] [[WorkshopMeetingName]] ","text_before_link":"<p>Bonjour,<br />\n<br />\nVeuillez confirmer vos dates de disponibilit&eacute;s pour la prochaine r&eacute;union [[WorkshopLongName]].<br />\n&nbsp;</p>\n","text_after_link":"<p><br />\n<br />\n[[WorkshopPresidentFullName]]<br />\n[[PresidentEmail]]<br />\n[[PresidentPhone]]<br />\n<br />\n&nbsp;</p>\n"}'
                ],
                    [
                    "setting_key" => "save_meeting_date_email_setting ",
                    "setting_value" => '{"email_subject":"La date de la prochaine réunion [[WorkshopLongName]] [[WorkshopMeetingName]] a été changée. ","text_before_link":"<p>Bonjour,<br />\n<br />\nMerci de bien vouloir r&eacute;server la date de la prochaine r&eacute;union [[WorkshopLongName]]&nbsp;le&nbsp; [[WorkshopMeetingDate]]&nbsp;&agrave; [[WorkshopMeetingTime]].<br />\n<br />\nElle aura lieu &agrave; l&#39;adresse suivante :<br />\n[[WorkshopMeetingAddress]].<br />\n<br />\n&nbsp;</p>\n","text_after_link":"<p><br />\n<br />\n[[WorkshopPresidentFullName]]<br />\n[[PresidentEmail]]<br />\n[[PresidentPhone]]<br />\n<br />\n&nbsp;</p>\n"}'
                    ],
                    [
                    "setting_key" => "save_new_meeting_date_email_setting",
                    "setting_value" => '{"email_subject":"La date de la prochaine réunion [[WorkshopLongName]] [[WorkshopMeetingName]].","text_before_link":"<p>Bonjour,<br />\n<br />\nMerci de bien vouloir r&eacute;server la nouvelle date de la prochaine r&eacute;union [[WorkshopLongName]]&nbsp;le&nbsp; [[WorkshopMeetingDate]]&nbsp;&agrave; [[WorkshopMeetingTime]].<br />\n<br />\nElle aura lieu &agrave; l&#39;adresse suivante :<br />\n[[WorkshopMeetingAddress]].<br />\n<br />\n&nbsp;</p>\n","text_after_link":"<p><br />\n<br />\n[[WorkshopPresidentFullName]]<br />\n[[PresidentEmail]]<br />\n[[PresidentPhone]]<br />\n<br />\n&nbsp;</p>\n"}'
                    ],
                [
                "setting_key" => "job_email_setting",
                "setting_value" => '{"email_subject":"Nouvelle tâche suite à la réunion [[WorkshopLongName]] [[WorkshopMeetingName]] du [[WorkshopMeetingDate]] à [[WorkshopMeetingTime]] ","text_before_link":"<p>Bonjour,<br />\n<br />\nVos t&acirc;ches suite &agrave; la r&eacute;union&nbsp; [[WorkshopLongName]]&nbsp; [[WorkshopMeetingName]] du&nbsp; [[WorkshopMeetingDate]] sont disponibles.<br />\n&nbsp;</p>\n","text_after_link":"<p><br />\n<br />\n&nbsp;</p>\n"}'
                ],
                [
                "setting_key" => "user_email_setting",
                "setting_value" => '{"email_subject":"Votre accès à la plateforme collaborative [[OrgShortName]]","text_before_link":"<p>Bonjour,<br />\n<br />\nBienvenue sur la plateforme collaborative [[OrgName]].<br />\n<br />\nPour votre premi&egrave;re connexion :<br />\nVotre login est votre email.<br />\nVotre mot de passe provisoire est votre email.<br />\nIl vous sera demand&eacute; de choisir votre mot de passe s&eacute;curis&eacute; lors de votre premi&egrave;re connexion.<br />\n<br />\nVous pouvez vous connecter en&nbsp;<br />\n<br />\n&nbsp;</p>\n","text_after_link":"<p><br />\n<br />\n&nbsp;</p>\n"}'
                ],
                [
                "setting_key" => "doodle_final_date",
                "setting_value" => '{"email_subject":"La date de la nouvelle réunion [[WorkshopLongName]] [[WorkshopMeetingName]] ","text_before_link":"<p>Bonjour,<br />\n<br />\nVoici la date finale pour la prochaine r&eacute;union&nbsp; [[WorkshopLongName]] le&nbsp; [[WorkshopMeetingDate]] &agrave; [[WorkshopMeetingTime]].\n<br />\n&nbsp;</p>\n","text_after_link":"<p><br />\n<br />\n[[WorkshopPresidentFullName]]<br />\n[[PresidentEmail]]<br />\n[[PresidentPhone]]</p>\n"}'
                ],
                [
                "setting_key" => "commission_new_user",
                "setting_value" => '{"email_subject":"Vous êtes invité ( e )  à rejoindre la commission ou groupe de travail suivant : [[WorkshopLongName]]","text_before_link":"<p>Bonjour<br />\n<br />\nBienvenue dans la commission ou groupe de travail suivant : [[WorkshopLongName]].<br />\n<br />\nPour votre premi&egrave;re connexion :<br />\nVotre login est votre email.<br />\nVotre mot de passe provisoire est votre email.<br />\nIl vous sera demand&eacute; de choisir votre mot de passe s&eacute;curis&eacute; lors de votre premi&egrave;re connexion.<br />\n<br />\n&nbsp;</p>\n","text_after_link":"<p><br />\n<br />\n[[WorkshopPresidentFullName]]<br />\n[[PresidentEmail]]<br />\n[[PresidentPhone]]<br />\n<br />\n&nbsp;</p>\n"}'
            ],
            [
                "setting_key"=>"msg_replies_email_setting" , 
                "setting_value"=>'{"email_subject":"La conversation que vous suivez a reçu une réponse","text_before_link":"<p>[[UserFirstName]]<br />\n<br />\nLa conversation que vous suivez a re&ccedil;u une r&eacute;ponse :<br />\nDe :&nbsp; [[WorkshopLongName]]&nbsp; [[MessageCategory]]<br />\n<br />\nPour voir le message original et ses r&eacute;ponses,&nbsp;</p>\n","text_after_link":""}'
            ],
            [
            "setting_key"=>"doodle_reminder_email_setting", 
            "setting_value"=>'{"email_subject":"Rappel : Choix des dates pour la prochaine réunion [[WorkshopLongName]] [[WorkshopMeetingName]] ","text_before_link":"<p>Bonjour,</p>\n\n<p>Veuillez confirmer vos dates de disponibilit&eacute;s pour la prochaine r&eacute;union&nbsp;&nbsp; [[WorkshopLongName]].</p>\n\n<p>&nbsp;</p>\n","text_after_link":"<p>[[WorkshopPresidentFullName]]</p>\n\n<p>[[PresidentEmail]]</p>\n\n<p>[[PresidentPhone]]</p>\n"}'
            ],
            [
            "setting_key" => "msg_push_setting",
            "setting_value" => '{"notification_text":""}'
            ],
            [
                "setting_key" => "agenda_push_setting",
                "setting_value" => '{"notification_text":""}'
            ],
            [
                "setting_key" => "decision_push_setting",
                "setting_value" => '{"notification_text":""}'
            ],
            [
                "setting_key" => "doodle_push_setting",
                "setting_value" => '{"notification_text":""}'
            ],
            [
                "setting_key" => "job_push_setting",
                "setting_value" => '{"notification_text":""}'
            ],
            [
                "setting_key" => "user_push_setting",
                "setting_value" => '{"notification_text":""}'
            ]
            ,
            [
                "setting_key" => "dashboard_setting",
                "setting_value" => '[{"name":"doodle","is_show":1,"order":1},{"name":"task","is_show":1,"order":2},{"name":"commissions","is_show":1,"order":3},{"name":"calendar","is_show":1,"order":4},{"name":"document","is_show":1,"order":5},{"name":"search","is_show":1,"order":6}]'
            ]
        ];
        DB::table('settings')->insert($data);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('settings');
    }

}
