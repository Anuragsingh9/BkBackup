<?php
    
    use App\Setting;
    use Illuminate\Database\Seeder;
    
    class SettingTableSeeder extends Seeder
    {
        /**
         * Run the database seeds.
         *
         * @return void
         */
        public function run()
        {
            $exclude = ['pdf_graphic', 'platform_graphic', 'email_graphic', 'push_notification_graphic', 'msg_email_setting', 'agenda_email_setting', 'decision_email_setting', 'doodle_email_setting', 'save_new_meeting_date_email_setting', 'save_meeting_date_email_setting', 'job_email_setting', 'user_email_setting', 'doodle_final_date', 'commission_new_user', 'msg_replies_email_setting', 'doodle_reminder_email_setting', 'overdue_alert',
                "qulification_welcome_workshop",
                "email_request_ready_notification_to_secratory_EN",
                "email_request_ready_notification_to_secratory_FR",
                "email_request_review_to_secratory_EN",
                "email_request_review_to_secratory_FR",
                "email_notification_to_expert_by_secratory_EN",
                "email_notification_to_expert_by_secrator_FR",
                "request_granted_EN",
                "request_granted_FR",
                "thanks_referrer_EN",
                "thanks_referrer_FR",
                "candidate_certificate_receive_EN",
                "candidate_certificate_receive_FR",
                "forgot_password_FR",
                "forgot_password_EN",
                "welcome_email_EN",
                "welcome_email_FR",
                "subscribed_non_installed_EN",
                "subscribed_non_installed_FR",
                "non_subscribed_installed_EN",
                "non_subscribed_installed_FR",
                "non_subscribed_non_installed_EN",
                "non_subscribed_non_installed_FR",
                "magic_link_received_EN",
                "magic_link_received_FR",
                "certificate_granted_EN",
                "certificate_granted_FR",
                "new_request_initiated_EN",
                "new_request_initiated_FR",
                "new_request_subscriber_non_installed_EN",
                "new_request_subscriber_non_installed_FR",
                "new_request_non_subscriber_installed_EN",
                "new_request_non_subscriber_installed_FR",
                "new_request_non_subscriber_non_installed_EN",
                "new_request_non_subscriber_non_installed_FR",
                "a_request_prevalidate_EN",
                "a_request_prevalidate_FR",
                "expert_request_is_ready_for_option_EN",
                "expert_request_is_ready_for_option_FR",
                "referrer_new_request_EN",
                "referrer_new_request_FR",
                "referrer_magic_link_submit_EN",
                "referrer_magic_link_submit_FR",
                "validation_code_EN",
                "validation_code_FR",
                "candidate_renewal_1_year_EN",
                "candidate_renewal_1_year_FR",
                "candidate_renewal_4_year_EN",
                "candidate_renewal_4_year_FR",
                "card_ranted_EN",
                "card_ranted_FR",
                "reminder_welcome_email_EN",
                "reminder_welcome_email_FR",
                "reminder_candidate_renewal_1_year_EN",
                "reminder_candidate_renewal_1_year_FR",
                "reminder_candidate_renewal_4_year_EN",
                "reminder_candidate_renewal_4_year_FR",
                "reminder_magic_link_submit_EN",
                "reminder_magic_link_submit_FR",
                "reminder_expert_EN",
                "reminder_expert_FR",
                "reminder_wkadmin_EN",
                "reminder_wkadmin_FR"
            ];
            $data = [
                [
                    "setting_key"   => "pdf_graphic",
                    "setting_value" => '{"color1":{"r":104,"g":201,"b":35,"a":1},"color2":{"r":221,"g":92,"b":147,"a":1},"footer_line1":"","footer_line2":"","header_logo":""}',
                ],
                [
                    "setting_key"   => "platform_graphic",
                    "setting_value" => '{"icon":""}',
                ],
                [
                    "setting_key"   => "email_graphic",
                    "setting_value" => '{"email_sign":"Dan2 Ake ADN\r\n\r\ndanakedirect@gmail.com\r\n\r\nTel : 01 42 78 00 00","top_banner":"header-img.jpg","bottom_banner":"footer-img.jpg"}',
                ],
                [
                    "setting_key"   => "push_notification_graphic",
                    "setting_value" => '{"icon":""}',
                ],
                [
                    "setting_key"   => "msg_email_setting",
                    "setting_value" => '{"email_subject":"Vous avez reçu un message","text_before_link":"<p>[[UserFirstName]]<br />\n<br />\nVous avez re&ccedil;u un message.<br />\nDe: [[WorkshopLongName]]&nbsp; [[MessageCategory]]<br />\n<br />\nPour r&eacute;pondre &agrave; ce message,</p>\n","text_after_link":""}',
                ],
                [
                    "setting_key"   => "agenda_email_setting",
                    "setting_value" => '{"email_subject":"Ordre du jour de la réunion [[WorkshopLongName]]  [[WorkshopMeetingName]] du [[WorkshopMeetingDate]]  à [[WorkshopMeetingTime]] ","text_before_link":"<p>Bonjour,<br />\n<br />\nVeuillez trouver ci-joint l&rsquo;ordre du jour de la r&eacute;union&nbsp; du&nbsp; [[WorkshopMeetingDate]] &agrave; [[WorkshopMeetingTime]], incluant les documents de travail.<br />\n&nbsp;</p>\n","text_after_link":"<p><br />\n<br />\n[[WorkshopPresidentFullName]]<br />\n[[PresidentEmail]]<br />\n[[PresidentPhone]]<br />\n<br />\n&nbsp;</p>\n"}',
                ],
                [
                    "setting_key"   => "decision_email_setting",
                    "setting_value" => '{"email_subject":"Relevé de décisions de la réunion [[WorkshopLongName]] [[WorkshopMeetingName]] du [[WorkshopMeetingDate]] à [[WorkshopMeetingTime]]","text_before_link":"<p>Bonjour,<br />\n<br />\nVeuillez trouver ci-joint le relev&eacute; de d&eacute;cisions de la r&eacute;union&nbsp; [[WorkshopLongName]] du&nbsp; [[WorkshopMeetingDate]] &agrave; [[WorkshopMeetingTime]].<br />\n&nbsp;</p>\n","text_after_link":"<p><br /><br />[[WorkshopPresidentFullName]]<br />\n[[PresidentEmail]]<br />\n[[PresidentPhone]]<br />\n&nbsp;</p>\n"}',
                ],
                
                [
                    "setting_key"   => "doodle_email_setting",
                    "setting_value" => '{"email_subject":"Choix des dates pour la prochaine réunion [[WorkshopLongName]] [[WorkshopMeetingName]] ","text_before_link":"<p>Bonjour,<br />\n<br />\nVeuillez confirmer vos dates de disponibilit&eacute;s pour la prochaine r&eacute;union [[WorkshopLongName]].<br />\n&nbsp;</p>\n","text_after_link":"<p><br />\n<br />\n[[WorkshopPresidentFullName]]<br />\n[[PresidentEmail]]<br />\n[[PresidentPhone]]<br />\n<br />\n&nbsp;</p>\n"}',
                ],
                [
                    "setting_key"   => "save_meeting_date_email_setting ",
                    "setting_value" => '{"email_subject":"La date de la prochaine réunion [[WorkshopLongName]] [[WorkshopMeetingName]] a été changée. ","text_before_link":"<p>Bonjour,<br />\n<br />\nMerci de bien vouloir r&eacute;server la date de la prochaine r&eacute;union [[WorkshopLongName]]&nbsp;le&nbsp; [[WorkshopMeetingDate]]&nbsp;&agrave; [[WorkshopMeetingTime]].<br />\n<br />\nElle aura lieu &agrave; l&#39;adresse suivante :<br />\n[[WorkshopMeetingAddress]].<br />\n<br />\n&nbsp;</p>\n","text_after_link":"<p><br />\n<br />\n[[WorkshopPresidentFullName]]<br />\n[[PresidentEmail]]<br />\n[[PresidentPhone]]<br />\n<br />\n&nbsp;</p>\n"}',
                ],
                [
                    "setting_key"   => "save_new_meeting_date_email_setting",
                    "setting_value" => '{"email_subject":"La date de la prochaine réunion [[WorkshopLongName]] [[WorkshopMeetingName]].","text_before_link":"<p>Bonjour,<br />\n<br />\nMerci de bien vouloir r&eacute;server la nouvelle date de la prochaine r&eacute;union [[WorkshopLongName]]&nbsp;le&nbsp; [[WorkshopMeetingDate]]&nbsp;&agrave; [[WorkshopMeetingTime]].<br />\n<br />\nElle aura lieu &agrave; l&#39;adresse suivante :<br />\n[[WorkshopMeetingAddress]].<br />\n<br />\n&nbsp;</p>\n","text_after_link":"<p><br />\n<br />\n[[WorkshopPresidentFullName]]<br />\n[[PresidentEmail]]<br />\n[[PresidentPhone]]<br />\n<br />\n&nbsp;</p>\n"}',
                ],
                [
                    "setting_key"   => "job_email_setting",
                    "setting_value" => '{"email_subject":"Nouvelle tâche suite à la réunion [[WorkshopLongName]] [[WorkshopMeetingName]] du [[WorkshopMeetingDate]] à [[WorkshopMeetingTime]] ","text_before_link":"<p>Bonjour,<br />\n<br />\nVos t&acirc;ches suite &agrave; la r&eacute;union&nbsp; [[WorkshopLongName]]&nbsp; [[WorkshopMeetingName]] du&nbsp; [[WorkshopMeetingDate]] sont disponibles.<br />\n&nbsp;</p>\n","text_after_link":"<p><br />\n<br />\n&nbsp;</p>\n"}',
                ],
                [
                    "setting_key"   => "user_email_setting",
                    "setting_value" => '{"email_subject":"Votre accès à la plateforme collaborative [[OrgShortName]]","text_before_link":"<p>Bonjour,<br />\n<br />\nBienvenue sur la plateforme collaborative [[OrgName]].<br />\n<br />\nPour votre premi&egrave;re connexion :<br />\nVotre login est votre email.<br />\nVotre mot de passe provisoire est votre email.<br />\nIl vous sera demand&eacute; de choisir votre mot de passe s&eacute;curis&eacute; lors de votre premi&egrave;re connexion.<br />\n<br />\nVous pouvez vous connecter en&nbsp;<br />\n<br />\n&nbsp;</p>\n","text_after_link":"<p><br />\n<br />\n&nbsp;</p>\n"}',
                ],
                [
                    "setting_key"   => "doodle_final_date",
                    "setting_value" => '{"email_subject":"La date de la nouvelle réunion [[WorkshopLongName]] [[WorkshopMeetingName]] ","text_before_link":"<p>Bonjour,<br />\n<br />\nVoici la date finale pour la prochaine r&eacute;union&nbsp; [[WorkshopLongName]] le&nbsp; [[WorkshopMeetingDate]] &agrave; [[WorkshopMeetingTime]].\n<br />\n&nbsp;</p>\n","text_after_link":"<p><br />\n<br />\n[[WorkshopPresidentFullName]]<br />\n[[PresidentEmail]]<br />\n[[PresidentPhone]]</p>\n"}',
                ],
                [
                    "setting_key"   => "commission_new_user",
                    "setting_value" => '{"email_subject":"Vous êtes invité ( e )  à rejoindre la commission ou groupe de travail suivant : [[WorkshopLongName]]","text_before_link":"<p>Bonjour<br />\n<br />\nBienvenue dans la commission ou groupe de travail suivant : [[WorkshopLongName]].<br />\n<br />\nPour votre premi&egrave;re connexion :<br />\nVotre login est votre email.<br />\nVotre mot de passe provisoire est votre email.<br />\nIl vous sera demand&eacute; de choisir votre mot de passe s&eacute;curis&eacute; lors de votre premi&egrave;re connexion.<br />\n<br />\n&nbsp;</p>\n","text_after_link":"<p><br />\n<br />\n[[WorkshopPresidentFullName]]<br />\n[[PresidentEmail]]<br />\n[[PresidentPhone]]<br />\n<br />\n&nbsp;</p>\n"}',
                ],
                [
                    "setting_key"   => "msg_replies_email_setting",
                    "setting_value" => '{"email_subject":"La conversation que vous suivez a reçu une réponse","text_before_link":"<p>[[UserFirstName]]<br />\n<br />\nLa conversation que vous suivez a re&ccedil;u une r&eacute;ponse :<br />\nDe :&nbsp; [[WorkshopLongName]]&nbsp; [[MessageCategory]]<br />\n<br />\nPour voir le message original et ses r&eacute;ponses,&nbsp;</p>\n","text_after_link":""}',
                ],
                [
                    "setting_key"   => "doodle_reminder_email_setting",
                    "setting_value" => '{"email_subject":"Rappel : Choix des dates pour la prochaine réunion [[WorkshopLongName]] [[WorkshopMeetingName]] ","text_before_link":"<p>Bonjour,</p>\n\n<p>Veuillez confirmer vos dates de disponibilit&eacute;s pour la prochaine r&eacute;union&nbsp;&nbsp; [[WorkshopLongName]].</p>\n\n<p>&nbsp;</p>\n","text_after_link":"<p>[[WorkshopPresidentFullName]]</p>\n\n<p>[[PresidentEmail]]</p>\n\n<p>[[PresidentPhone]]</p>\n"}',
                ],
                [
                    "setting_key"   => "msg_push_setting",
                    "setting_value" => '{"title":"Vous avez reçu un message","notification_text":"<p>Vous avez re&ccedil;u un message de&nbsp; [[WorkshopLongName]]&nbsp; [[MessageCategory]]&nbsp;.</p>\n\n<p>&nbsp;</p>\n\n<p><br />\n&nbsp;​​</p>\n"}',
                ],
                [
                    "setting_key"   => "personal_email_setting",
                    "setting_value" => '{"email_subject":"Vous avez reçu un message","text_before_link":"<p>[[UserFirstName]]<br />\n<br />\nVous avez re&ccedil;u un message personnel.<br />\nDe: [[UserSenderFirstName]]&nbsp; [[UserSenderLastName]]</p>\n\n<p>Pour r&eacute;pondre &agrave; ce message,</p>\n","text_after_link":""}',
                ],
                [
                    "setting_key"   => "agenda_push_setting",
                    "setting_value" => '{"title":"Envoi de l\'ordre du jour","notification_text":"<p>Veuillez trouver ci-joint l&rsquo;ordre du jour de la r&eacute;union [[WorkshopLongName]] du [[WorkshopMeetingDate]] &agrave; [[WorkshopMeetingTime]].</p>\n"}',
                ],
                [
                    "setting_key"   => "decision_push_setting",
                    "setting_value" => '{"title":"Envoi de relevé de décisions","notification_text":"<p>Veuillez trouver ci-joint le relev&eacute; de d&eacute;cisions de la r&eacute;union [[WorkshopLongName]] du [[WorkshopMeetingDate]] &agrave; [[WorkshopMeetingTime]]</p>\n\n<p>&nbsp;</p>\n"}',
                ],
                [
                    "setting_key"   => "doodle_push_setting",
                    "setting_value" => '{"title":"Envoi de Doodle","notification_text":"<p>Veuillez confirmer vos dates de disponibilit&eacute;s pour la prochaine r&eacute;union [[WorkshopLongName]].</p>\n"}',
                ],
                [
                    "setting_key"   => "message_reply_push_setting",
                    "setting_value" => '{"title":"Une réponse à été apportée à votre conversation","notification_text":"<p>La conversation que vous suivez a re&ccedil;u une r&eacute;ponse de&nbsp; [[WorkshopLongName]] [[MessageCategory]]</p>\n"}',
                ],
                [
                    "setting_key"   => "personal_message_push_setting",
                    "setting_value" => '{"title":"Vous avez reçu un message personnel","notification_text":"<p>Vous avez re&ccedil;u un message personnel de&nbsp; [[UserSenderFirstName]] [[UserSenderLastName]].</p>\n"}',
                ],
                [
                    "setting_key"   => "save_metting_push_setting",
                    "setting_value" => '{"title":"Save the date","notification_text":"<p>Merci de bien vouloir r&eacute;server la date de la prochaine r&eacute;union [[WorkshopLongName]] le [[WorkshopMeetingDate]] &agrave; [[WorkshopMeetingTime]].</p>\n"}',
                ],
                [
                    "setting_key"   => "save_modify_metting_push_setting",
                    "setting_value" => '{"title":"Date modifiée","notification_text":"<p>Merci de bien vouloir r&eacute;server la nouvelle date de la prochaine r&eacute;union [[WorkshopLongName]] le [[WorkshopMeetingDate]] &agrave; [[WorkshopMeetingTime]].</p>\n"}',
                ],
                
                [
                    "setting_key"   => "doodle_reminder_push_setting",
                    "setting_value" => '{"title":"Rappel de Doodle","notification_text":"<p>Merci de bien vouloir r&eacute;server la nouvelle date de la prochaine r&eacute;union [[WorkshopLongName]] le [[WorkshopMeetingDate]] &agrave; [[WorkshopMeetingTime]].</p>\n"}',
                ],
                [
                    "setting_key"   => "save_modify_metting_push_setting",
                    "setting_value" => '{"title":"Date modifiée","notification_text":"<p>Merci de bien vouloir r&eacute;server la nouvelle date de la prochaine r&eacute;union [[WorkshopLongName]] le [[WorkshopMeetingDate]] &agrave; [[WorkshopMeetingTime]].</p>\n"}',
                ],
                [
                    "setting_key"   => "doodle_final_push_setting",
                    "setting_value" => '{"title":"Doodle Date Finale","notification_text":"<p>Voici la date finale pour la prochaine r&eacute;union &nbsp; [[WorkshopLongName]] le [[WorkshopMeetingDate]] &agrave; [[WorkshopMeetingTime]].</p>\n"}',
                ],
                
                [
                    "setting_key"   => "dashboard_setting",
                    "setting_value" => '[{"name":"doodle","is_show":1,"order":1},{"name":"task","is_show":1,"order":2},{"name":"commissions","is_show":1,"order":3},{"name":"calendar","is_show":1,"order":4},{"name":"document","is_show":1,"order":5},{"name":"search","is_show":1,"order":6},{"name":"project","is_show":0,"order":7}]',
                ],
                [
                    'setting_key'   => 'overdue_alert',
                    'setting_value' => '{"color":"#ff0000"}',
                ]
                ,
                [
                    "setting_key"   => "send_project_task",
                    "setting_value" => '{"email_subject":"New task for project  [[WorkshopLongName]]  [[ProjectName]] ","text_before_link":"<p>Hi,<br />\n<br />\nYour tasks for a project&nbsp;[[WorkshopLongName]]&nbsp;​​​​[[ProjectName]]&nbsp;are available.</p>\n","text_after_link":""}',
                ],
                [
                    "setting_key"   => "modify_task_permission_request_admin",
                    "setting_value" => '{"email_subject":"You have got a task modification request for project [[WorkshopLongName]] [[ProjectName]] ","text_before_link":"<p>Hi,<br />\n<br />\nYour task permission requests for project [[WorkshopLongName]]&nbsp;[[ProjectName]] are available.</p>\n","text_after_link":""}',
                ],
                [
                    "setting_key"   => "modify_task_permission_accept",
                    "setting_value" => '{"email_subject":"The status of your task modification request for project [[WorkshopLongName]] [[ProjectName]] ","text_before_link":"<p>Hi,<br />\n<br />\nYour task permission requests for project [[WorkshopLongName]]&nbsp;&nbsp;[[ProjectName]]&nbsp;has been accepted.</p>\n","text_after_link":""}',
                ],
                [
                    "setting_key"   => "modify_task_permission_reject",
                    "setting_value" => '{"email_subject":"The status of your  task modification request for project [[WorkshopLongName]]  [[ProjectName]] ","text_before_link":"<p>Hi,<br />\n<br />\nYour task permission ,requests for [[TaskName]], project [[WorkshopLongName]]&nbsp;[[ProjectName]]&nbsp;has been rejected.</p>\n","text_after_link":""}',
                ],
                [
                    "setting_key"   => "msg_push_setting_EN",
                    "setting_value" => '{"title":"You have got a message","notification_text":"<p>You\'ve got a the new message from [[WorkshopLongName]] [[MessageCategory]]&nbsp;.</p>\n\n<p>&nbsp;</p>\n\n<p><br />\n&nbsp;​​</p>\n"}',
                ],
                [
                    "setting_key"   => "agenda_push_setting_EN",
                    "setting_value" => '{"title":"Sending meeting agenda","notification_text":"<p>Here is the agenda of the meeting [[WorkshopLongName]] on [[WorkshopMeetingDate]] at [[WorkshopMeetingTime]].</p>\n"}',
                ],
                [
                    "setting_key"   => "decision_push_setting_EN",
                    "setting_value" => '{"title":"Sending meeting Report","notification_text":"<p>Here is the report of the meeting  [[WorkshopLongName]] on [[WorkshopMeetingDate]] at [[WorkshopMeetingTime]]</p>\n\n<p>&nbsp;</p>\n"}',
                ],
                [
                    "setting_key"   => "doodle_push_setting_EN",
                    "setting_value" => '{"title":"Sending meeting Doodle","notification_text":"<p>Please confirm your availabilities for the next meeting [[WorkshopLongName]]. </p>\n"}',
                ],
                [
                    "setting_key"   => "message_reply_push_setting_EN",
                    "setting_value" => '{"title":"A reply in your conversation","notification_text":"<p>The conversation you\'re following has got an answer from [[WorkshopLongName]] [[MessageCategory]].</p>\n"}',
                ],
                [
                    "setting_key"   => "personal_message_push_setting_EN",
                    "setting_value" => '{"title":"You have got a personal message","notification_text":"<p>The conversation you\'re following has got an answer from  [[WorkshopLongName]] [[MessageCategory]].</p>\n"}',
                ],
                [
                    "setting_key"   => "save_metting_push_setting_EN",
                    "setting_value" => '{"title":"Sending Save the Date","notification_text":"<p>Thanks for booking the date of the next meeting [[WorkshopLongName]] on [[WorkshopMeetingDate]] at [[WorkshopMeetingTime]].</p>\n"}',
                ],
                
                
                [
                    "setting_key"   => "doodle_reminder_push_setting_EN",
                    "setting_value" => '{"title":"Sending Doodle Reminder","notification_text":"<p>Thanks to confirm your availabilities for next meeting  [[WorkshopLongName]].</p>\n"}',
                ],
                [
                    "setting_key"   => "save_modify_metting_push_setting_EN",
                    "setting_value" => '{"title":"Sending Modify Meeting","notification_text":"<p>Thanks for booking the new date of the next meeting [[WorkshopLongName]] on [[WorkshopMeetingDate]] at [[WorkshopMeetingTime]].</p>\n"}',
                ],
                [
                    "setting_key"   => "doodle_final_push_setting_EN",
                    "setting_value" => '{"title":"Sending Doodle Final Date","notification_text":"<p>Here is the final date for the next meeting [[WorkshopLongName]] on [[WorkshopMeetingDate]] at [[WorkshopMeetingTime]].</p>\n"}',
                ],
                [
                    "setting_key"   => "invite_member_email_notification_list",
                    "setting_value" => '',
                ],
                [
                    "setting_key"   => "alert_new_member_email_EN",
                    "setting_value" => '{"email_subject":"A new member has been added to  [[WorkshopLongName]] ","text_before_link":"[[UserFirstName]]&nbsp; [[UserLastName]],&nbsp;[[UserEmail]] has been added to&nbsp; [[WorkshopLongName]]<br />\n&nbsp;","text_after_link":""}',
                ],
                [
                    "setting_key"   => "alert_new_member_email",
                    "setting_value" => '{"email_subject":"Un nouveau membre a été ajouté à  [[WorkshopLongName]] ","text_before_link":"[[UserFirstName]]&nbsp; [[UserLastName]],&nbsp;[[UserEmail]] a &eacute;t&eacute; ajout&eacute; &agrave;&nbsp; [[WorkshopLongName]]<br />\n&nbsp;","text_after_link":""}',
                ],
                // [
                //     "setting_key" => "qulification_welcome_workshop",
                //     "setting_value" => '{"email_subject":"[[OrgName]] : Accès à la plateforme d&apos;attribution de la carte TP-Pro","text_before_link":"<p>Bonjour,<br />\n<br />\nCette plateforme vous permettra de faire votre demande de Carte TP -Pro, de suivre votre dossier et d&apos;obtenir tous les éléments de vote qualification.<br />\n&nbsp;Pour votre première connexion :<br />&nbsp;- Votre login est votre email.<br />&nbsp; - Votre mot de passe provisoire est votre email.<br />Il vous sera demandé de choisir votre mot de passe sécurisé lors de votre première connexion.</p>\n","text_after_link":"<p>Pour se connecter :<br />\n<br />\nBien cordialement<br />,\n[[WorkshopPresidentFullName]]<br />\n[[PresidentEmail]]<br />\n<br />\n&nbsp;</p>\n"}'
                // ],
                // [
                //     "setting_key" => "email_request_ready_notification_to_secratory_EN",
                //     "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}'
                // ],
                // [
                //     "setting_key" => "email_request_ready_notification_to_secratory_FR",
                //     "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}'
                // ],
                // [
                //     "setting_key" => "email_request_review_to_secratory_EN",
                //     "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}'
                // ],
                // [
                //     "setting_key" => "email_request_review_to_secratory_FR",
                //     "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}'
                // ],
                // [
                //     "setting_key" => "email_notification_to_expert_by_secratory_EN",
                //     "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}'
                // ],
                // [
                //     "setting_key" => "email_notification_to_expert_by_secrator_FR",
                //     "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}'
                // ],
                // [
                //     "setting_key" => "request_granted_EN",
                //     "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}'
                // ],
                // [
                //     "setting_key" => "request_granted_FR",
                //     "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}'
                // ],
                // [
                //     "setting_key" => "thanks_referrer_EN",
                //     "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}'
                // ],
                // [
                //     "setting_key" => "thanks_referrer_FR",
                //     "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}'
                // ],
                // [
                //     "setting_key" => "candidate_certificate_receive_EN",
                //     "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}'
                // ],
                // [
                //     "setting_key" => "candidate_certificate_receive_FR",
                //     "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}'
                // ],Accès à la plateforme d'attribution de la carte TP-Pro
                [
                    "setting_key"   => "qulification_welcome_workshop",
                    "setting_value" => '{"email_subject":"[[OrgName]] : Accès à la plateforme d\'attribution de la carte TP-Pro","text_before_link":"<p>Bonjour,<br />\n<br />\nCette plateforme vous permettra de faire votre demande de Carte TP -Pro, de suivre votre dossier et d&apos;obtenir tous les éléments de vote qualification.<br />\n&nbsp;Pour votre première connexion :<br />&nbsp;- Votre login est votre email.<br />&nbsp; - Votre mot de passe provisoire est votre email.<br />Il vous sera demandé de choisir votre mot de passe sécurisé lors de votre première connexion.</p>\n","text_after_link":"<p>Pour se connecter :<br />\n<br />\nBien cordialement<br />,\n[[WorkshopPresidentFullName]]<br />\n[[PresidentEmail]]<br />\n<br />\n&nbsp;</p>\n"}',
                ],
                [
                    "setting_key"   => "email_request_ready_notification_to_secratory_EN",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "email_request_ready_notification_to_secratory_FR",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "email_request_review_to_secratory_EN",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "email_request_review_to_secratory_FR",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "email_notification_to_expert_by_secratory_EN",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "email_notification_to_expert_by_secrator_FR",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "request_granted_EN",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "request_granted_FR",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "thanks_referrer_EN",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "thanks_referrer_FR",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "candidate_certificate_receive_EN",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "candidate_certificate_receive_FR",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "forgot_password_FR",
                    "setting_value" => '{"header_bar":"Plateforme N°1 pour organisations professionnelles","welcome_text1":"Bienvenue sur OP Simplify","welcome_text2":"Send","change_text":"Changez votre mot de passe","caption_text1":"Choisissez votre nouveau mot de passe","caption_text2":"Entrez à nouveau votre mot de passe","button_text1":"Mettre à jour"}',
                ],
                [
                    "setting_key"   => "forgot_password_EN",
                    "setting_value" => '{"header_bar":"Plateforme N°1 pour organisations professionnelles","welcome_text1":"Bienvenue sur OP Simplify","welcome_text2":"Send","change_text":"Changez votre mot de passe","caption_text1":"Choisissez votre nouveau mot de passe","caption_text2":"Entrez à nouveau votre mot de passe","button_text1":"Mettre à jour"}',
                ],
                [
                    "setting_key"   => "welcome_email_EN",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "welcome_email_FR",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "subscribed_non_installed_EN",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "subscribed_non_installed_FR",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "non_subscribed_installed_EN",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "non_subscribed_installed_FR",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "non_subscribed_non_installed_EN",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "non_subscribed_non_installed_FR",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "magic_link_received_EN",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "magic_link_received_FR",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "certificate_granted_EN",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "certificate_granted_FR",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                
                [
                    "setting_key"   => "new_request_initiated_EN",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "new_request_initiated_FR",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "new_request_subscriber_non_installed_EN",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "new_request_subscriber_non_installed_FR",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "new_request_non_subscriber_installed_EN",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "new_request_non_subscriber_installed_FR",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "new_request_non_subscriber_non_installed_EN",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "new_request_non_subscriber_non_installed_FR",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "a_request_prevalidate_EN",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "a_request_prevalidate_FR",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "expert_request_is_ready_for_option_EN",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "expert_request_is_ready_for_option_FR",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "referrer_new_request_EN",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "referrer_new_request_FR",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "referrer_magic_link_submit_EN",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "referrer_magic_link_submit_FR",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "validation_code_EN",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_code":"text_before_code\n","text_between_code_and_link":"text_between_code_and_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "validation_code_FR",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_code":"text_before_code\n","text_between_code_and_link":"text_between_code_and_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "candidate_renewal_1_year_EN",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "candidate_renewal_1_year_FR",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "candidate_renewal_4_year_EN",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "candidate_renewal_4_year_FR",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "card_ranted_EN",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "card_ranted_FR",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "reminder_welcome_email_EN",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "reminder_welcome_email_FR",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "reminder_candidate_renewal_1_year_EN",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_code":"text_before_code\n","text_between_code_and_link":"text_between_code_and_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "reminder_candidate_renewal_1_year_FR",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_code":"text_before_code\n","text_between_code_and_link":"text_between_code_and_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "reminder_candidate_renewal_4_year_EN",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_code":"text_before_code\n","text_between_code_and_link":"text_between_code_and_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "reminder_candidate_renewal_4_year_FR",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_code":"text_before_code\n","text_between_code_and_link":"text_between_code_and_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "reminder_magic_link_submit_EN",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "reminder_magic_link_submit_FR",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "reminder_expert_EN",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "reminder_expert_FR",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "reminder_wkadmin_EN",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "reminder_wkadmin_FR",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "event_register_FR",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "event_register_EN",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "event_modify_FR",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "event_modify_EN",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "save_video_meeting_date_email_setting_EN",
                    "setting_value" => '{"email_subject":"The date for the next meeting  [[WorkshopLongName]] [[WorkshopMeetingName]].","text_before_link":"Hi,<br><br>\nPlease save the date for next meeting [[WorkshopLongName]] on [[WorkshopMeetingDate]] at <[[WorkshopMeetingTime]]>.<br><br>This is a video meeting.<br><br>","text_after_link":"[[WorkshopPresidentFullName]]<br>[[PresidentEmail]]<br>[[PresidentPhone]]<br><br>"}',
                ],
                [
                    "setting_key"   => "save_video_meeting_date_email_setting_FR",
                    "setting_value" => '{"email_subject":"The date for the next meeting  [[WorkshopLongName]] [[WorkshopMeetingName]].","text_before_link":"Hi,<br><br>\nPlease save the date for next meeting [[WorkshopLongName]] on [[WorkshopMeetingDate]] at <[[WorkshopMeetingTime]]>.<br><br>This is a video meeting.<br><br>","text_after_link":"[[WorkshopPresidentFullName]]<br>[[PresidentEmail]]<br>[[PresidentPhone]]<br><br>"}',
                ],
                [
                    "setting_key"   => "save_new_video_meeting_date_email_setting_EN",
                    "setting_value" => '{"email_subject":"The date for the next meeting  [[WorkshopLongName]] [[WorkshopMeetingName]] has been changed.","text_before_link":"Hi,<br><br>\nPlease save the new date for next meeting [[WorkshopLongName]] on [[WorkshopMeetingDate]] at <[[WorkshopMeetingTime]]>.<br><br>This is a video meeting.<br><br>","text_after_link":"[[WorkshopPresidentFullName]]<br>[[PresidentEmail]]<br>[[PresidentPhone]]<br><br>"}',
                ],
                [
                    "setting_key"   => "save_new_video_meeting_date_email_setting_FR",
                    "setting_value" => '{"email_subject":"The date for the next meeting  [[WorkshopLongName]] [[WorkshopMeetingName]] has been changed.","text_before_link":"Hi,<br><br>\nPlease save the new date for next meeting [[WorkshopLongName]] on [[WorkshopMeetingDate]] at <[[WorkshopMeetingTime]]>.<br><br>This is a video meeting.<br><br>","text_after_link":"[[WorkshopPresidentFullName]]<br>[[PresidentEmail]]<br>[[PresidentPhone]]<br><br>"}',
                ],
                [
                    "setting_key"   => "doodle_video_meeting_email_setting_EN",
                    "setting_value" => '{"email_subject":"Choice of dates for next meeting [[WorkshopLongName]] [[WorkshopMeetingName]]","text_before_link":"HI,<br><br>\nPlease confirm your availability dates for the next meeting [[WorkshopLongName]].<br>This is a video meeting.<br><br>","text_after_link":"[[WorkshopPresidentFullName]]<br>[[PresidentEmail]]<br>[[PresidentPhone]]<br><br>"}',
                ],
                [
                    "setting_key"   => "doodle_video_meeting_email_setting_FR",
                    "setting_value" => '{"email_subject":"Choice of dates for next meeting [[WorkshopLongName]] [[WorkshopMeetingName]]","text_before_link":"HI,<br><br>\nPlease confirm your availability dates for the next meeting [[WorkshopLongName]].<br>This is a video meeting.<br><br>","text_after_link":"[[WorkshopPresidentFullName]]<br>[[PresidentEmail]]<br>[[PresidentPhone]]<br><br>"}',
                ],
                [
                    "setting_key"   => "doodle_video_meeting_reminder_email_setting_EN",
                    "setting_value" => '{"email_subject":"Reminder : Choice of dates for the next meeting [[WorkshopLongName]] [[WorkshopMeetingName]]","text_before_link":"HI,<br><br>\nPlease confirm your availability dates for the next meeting [[WorkshopLongName]].<br>This is a video meeting.<br><br>","text_after_link":"[[WorkshopPresidentFullName]]<br>[[PresidentEmail]]<br>[[PresidentPhone]]<br><br>"}',
                ],
                [
                    "setting_key"   => "doodle_video_meeting_reminder_email_setting_FR",
                    "setting_value" => '{"email_subject":"Reminder : Choice of dates for the next meeting [[WorkshopLongName]] [[WorkshopMeetingName]]","text_before_link":"HI,<br><br>\nPlease confirm your availability dates for the next meeting [[WorkshopLongName]].<br>This is a video meeting.<br><br>","text_after_link":"[[WorkshopPresidentFullName]]<br>[[PresidentEmail]]<br>[[PresidentPhone]]<br><br>"}',
                ],
                [
                    "setting_key"   => "doodle_video_meeting_final_date_EN",
                    "setting_value" => '{"email_subject":"The date for the next meeting  [[WorkshopLongName]] [[WorkshopMeetingName]]","text_before_link":"Hi,<br><br>\nHere is the final date for next meeting [[WorkshopLongName]] on [[WorkshopMeetingDate]] at <[[WorkshopMeetingTime]]>.<br>This is a video meeting.<br><br>","text_after_link":"[[WorkshopPresidentFullName]]<br>[[PresidentEmail]]<br>[[PresidentPhone]]<br><br>"}',
                ],
                [
                    "setting_key"   => "doodle_video_meeting_final_date_FR",
                    "setting_value" => '{"email_subject":"The date for the next meeting  [[WorkshopLongName]] [[WorkshopMeetingName]]","text_before_link":"Hi,<br><br>\nHere is the final date for next meeting [[WorkshopLongName]] on [[WorkshopMeetingDate]] at <[[WorkshopMeetingTime]]>.<br>This is a video meeting.<br><br>","text_after_link":"[[WorkshopPresidentFullName]]<br>[[PresidentEmail]]<br>[[PresidentPhone]]<br><br>"}',
                ],
                // Hybrid Video meeting
                [
                    "setting_key"   => "save_hybrid_meeting_date_email_setting_EN",
                    "setting_value" => '{"email_subject":"The date for the next meeting  [[WorkshopLongName]] [[WorkshopMeetingName]].","text_before_link":"Hi,<br><br>\nPlease save the date for next meeting [[WorkshopLongName]] on [[WorkshopMeetingDate]] at <[[WorkshopMeetingTime]]>.<br><br>Location will be [[WorkshopMeetingAddress]].<br><br>Those who cannot attend will be able to join video meeting.<br><br>","text_after_link":"[[WorkshopPresidentFullName]]<br>[[PresidentEmail]]<br>[[PresidentPhone]]<br><br>"}',
                ],
                [
                    "setting_key"   => "save_hybrid_meeting_date_email_setting_FR",
                    "setting_value" => '{"email_subject":"The date for the next meeting  [[WorkshopLongName]] [[WorkshopMeetingName]].","text_before_link":"Hi,<br><br>\nPlease save the date for next meeting [[WorkshopLongName]] on [[WorkshopMeetingDate]] at <[[WorkshopMeetingTime]]>.<br><br>Location will be [[WorkshopMeetingAddress]].<br><br>Those who cannot attend will be able to join video meeting.<br><br>","text_after_link":"[[WorkshopPresidentFullName]]<br>[[PresidentEmail]]<br>[[PresidentPhone]]<br><br>"}',
                ],
                [
                    "setting_key"   => "save_new_hybrid_meeting_date_email_setting_EN",
                    "setting_value" => '{"email_subject":"The date for the next meeting  [[WorkshopLongName]] [[WorkshopMeetingName]] has been changed.","text_before_link":"Hi,<br><br>\nPlease save the new date for next meeting [[WorkshopLongName]] on [[WorkshopMeetingDate]] at <[[WorkshopMeetingTime]]>.<br><br>Location will be [[WorkshopMeetingAddress]].<br><br>Those who cannot attend will be able to join video meeting.<br><br>","text_after_link":"[[WorkshopPresidentFullName]]<br>[[PresidentEmail]]<br>[[PresidentPhone]]<br><br>"}',
                ],
                [
                    "setting_key"   => "save_new_hybrid_meeting_date_email_setting_FR",
                    "setting_value" => '{"email_subject":"The date for the next meeting  [[WorkshopLongName]] [[WorkshopMeetingName]] has been changed.","text_before_link":"Hi,<br><br>\nPlease save the new date for next meeting [[WorkshopLongName]] on [[WorkshopMeetingDate]] at <[[WorkshopMeetingTime]]>.<br><br>Location will be [[WorkshopMeetingAddress]].<br><br>Those who cannot attend will be able to join video meeting.<br><br>","text_after_link":"[[WorkshopPresidentFullName]]<br>[[PresidentEmail]]<br>[[PresidentPhone]]<br><br>"}',
                ],
                [
                    "setting_key"   => "doodle_hybrid_meeting_email_setting_EN",
                    "setting_value" => '{"email_subject":"Choice of dates for next meeting [[WorkshopLongName]] [[WorkshopMeetingName]]","text_before_link":"HI,<br><br>\nPlease confirm your availability dates for the next meeting [[WorkshopLongName]].<br>This is an hybrid meeting. Those who cannot join will be able to participe in video meeting.<br><br>","text_after_link":"[[WorkshopPresidentFullName]]<br>[[PresidentEmail]]<br>[[PresidentPhone]]<br><br>"}',
                ],
                [
                    "setting_key"   => "doodle_hybrid_meeting_email_setting_FR",
                    "setting_value" => '{"email_subject":"Choice of dates for next meeting [[WorkshopLongName]] [[WorkshopMeetingName]]","text_before_link":"HI,<br><br>\nPlease confirm your availability dates for the next meeting [[WorkshopLongName]].<br>This is an hybrid meeting. Those who cannot join will be able to participe in video meeting.<br><br>","text_after_link":"[[WorkshopPresidentFullName]]<br>[[PresidentEmail]]<br>[[PresidentPhone]]<br><br>"}',
                ],
                [
                    "setting_key"   => "doodle_hybrid_meeting_reminder_email_setting_EN",
                    "setting_value" => '{"email_subject":"Reminder : Choice of dates for the next meeting [[WorkshopLongName]] [[WorkshopMeetingName]]","text_before_link":"HI,<br><br>\nPlease confirm your availability dates for the next meeting [[WorkshopLongName]].<br>This is an hybrid meeting. Those who cannot join will be able to participe in video meeting.<br><br>","text_after_link":"[[WorkshopPresidentFullName]]<br>[[PresidentEmail]]<br>[[PresidentPhone]]<br><br>"}',
                ],
                [
                    "setting_key"   => "doodle_hybrid_meeting_reminder_email_setting_FR",
                    "setting_value" => '{"email_subject":"Reminder : Choice of dates for the next meeting [[WorkshopLongName]] [[WorkshopMeetingName]]","text_before_link":"HI,<br><br>\nPlease confirm your availability dates for the next meeting [[WorkshopLongName]].<br>This is an hybrid meeting. Those who cannot join will be able to participe in video meeting.<br><br>","text_after_link":"[[WorkshopPresidentFullName]]<br>[[PresidentEmail]]<br>[[PresidentPhone]]<br><br>"}',
                ],
                [
                    "setting_key"   => "doodle_hybrid_meeting_final_date_EN",
                    "setting_value" => '{"email_subject":"The date for the next meeting  [[WorkshopLongName]] [[WorkshopMeetingName]]","text_before_link":"Hi,<br><br>\nHere is the final date for next meeting [[WorkshopLongName]] on [[WorkshopMeetingDate]] at <[[WorkshopMeetingTime]]>.<br>This is an hybrid meeting. Those who cannot join will be able to participe in video meeting.<br><br>","text_after_link":"[[WorkshopPresidentFullName]]<br>[[PresidentEmail]]<br>[[PresidentPhone]]<br><br>"}',
                ],
                [
                    "setting_key"   => "doodle_hybrid_meeting_final_date_FR",
                    "setting_value" => '{"email_subject":"The date for the next meeting  [[WorkshopLongName]] [[WorkshopMeetingName]]","text_before_link":"Hi,<br><br>\nHere is the final date for next meeting [[WorkshopLongName]] on [[WorkshopMeetingDate]] at <[[WorkshopMeetingTime]]>.<br>This is an hybrid meeting. Those who cannot join will be able to participe in video meeting.<br><br>","text_after_link":"[[WorkshopPresidentFullName]]<br>[[PresidentEmail]]<br>[[PresidentPhone]]<br><br>"}',
                ],
                // keep contact
                [
                    "setting_key"   => "event_magic_link_EN",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "event_magic_link_FR",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "event_validation_code_EN",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "event_validation_code_FR",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "languages_to_show",
                    "setting_value" => '["EN", "FR"]',
                ],
            ];
            $allowedToReplace = [];
            foreach ($data as $key => $value) {
                $exist = Setting::where('setting_key', $value['setting_key']);
                if ($exist->count() == 0) {
                    Setting::updateOrCreate(['setting_key' => $value['setting_key']], $value);
                } else {
                    $dbVal = $exist->first();
                    $decode = json_decode($dbVal->setting_value);
                    if (isJson($value['setting_value'])) {
                        $decodeValue = json_decode($value['setting_value']);
                    } else {
                        $decodeValue = json_decode(preg_replace('/\s+/', '', $value['setting_value']));
                    }
                    $decode = collect($decode);
                    $newArray = [];
                    collect($decodeValue)->each(function ($v, $k) use ($decode, $decodeValue, &$newArray, $allowedToReplace, $value) {

                        if ($decode->has($k)) {
                            if (isset($allowedToReplace[$value['setting_key']]) && in_array($k, $allowedToReplace[$value['setting_key']])) {
                                $newArray[$k] = $v;
                            } else {
                                $newArray[$k] = isset($decode[$k]) ? $decode[$k] : $v;
                            }
                        } else {
                            $newArray[$k] = $v;
                        }
                    });
                    $exist->update(['setting_value' => json_encode($newArray)]);
                }
            }
            /*foreach ($data as $key => $value) {
                if (!in_array($value['setting_key'], $exclude)) {
                    Setting::updateOrCreate(['setting_key' => $value['setting_key']], $value);
                }
            }*/
        }
    }

