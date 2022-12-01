<?php

    namespace Modules\Resilience\Database\Seeders;

    use App\Setting;
    use Illuminate\Database\Seeder;
    use Illuminate\Database\Eloquent\Model;

    class SeedSettingTableTableSeeder extends Seeder
    {
        /**
         * Run the database seeds.
         *
         * @return void
         */
        public function run()
        {
            $graphics = getSettingData('graphic_config', 1);
            $defaultPage = '{"title":"Professional Associations Think Tank France","description":"description","logo":null,"bottomImage":"uploads/consultation-defaults/progress-speed.bfb7ea72.png","bottomText":"100% du profit de ce projet sont re-investis dans le combat contre la crise", "footerTextLineOne":"<p><strong>Combattre la crise</strong>, Créer des liens avec des pairs,</p>", "footerTextLineTwo":"<p>Rendre à nouveau <strong>attractives les organisations professionnelles.</strong></p>","website":"www.example.com","twitter":"www.twitter.com","linkedin":"www.linkedin.com","facebook":"www.facebook.com","instagram":"www.instagram.com","replayText":"replay text","sectionLineOne":"<p>Travailler <strong>ensemble</strong></p>","sectionLineTwo":"<p>pour <strong>redéfinir</strong> nos forces</p>","lightTextColor":{"color":{"r":255,"g":255,"b":255,"a":1}},"mediumTextColor":{"color":{"r":110,"g":110,"b":133,"a":1}},"darkTextColor":{"color":{"r":7,"g":68,"b":159,"a":1}},
"headerBackgroundColor":{"color":{"r":255,"g":255,"b":255,"a":1}},
"headerTextColor":{"color":{"r":255,"g":219,"b":50,"a":1}},
"runningManColor":{"color":{"r":6,"g":6,"b":134,"a":1}},
"highlightTextColor":{"color":{"r":247,"g":172,"b":21,"a":1}},"lightBackGroundColor":{"color":{"r":74,"g":137,"b":196,"a":1}},"mediumBackGroundColor":{"color":{"r":74,"g":137,"b":196,"a":1}},"darkBackGroundColor":{"color":{"r":2,"g":85,"b":121,"a":1}},
"footerTextColor":{"color":{"r":255,"g":255,"b":255,"a":1}},
"highlightBackGroundColor":{"color":{"r":113,"g":173,"b":230,"a":1}},"bottomTextColor":{"color":{"r":255,"g":255,"b":255,"a":1}},"shapeBackColor":{"color":{"r":74,"g":137,"b":196,"a":1}},"shapeActiveBackColor":{"color":{"r":255,"g":255,"b":255,"a":1}},"stickerGradiantLeftColor":{"color":{"r":7,"g":103,"b":152,"a":1}},"stickerActiveGradiantLeftColor":{"color":{"r":239,"g":207,"b":16,"a":1}},"stickerActiveGradiantRightColor":{"color":{"r":249,"g":162,"b":23,"a":1}},"stickerGradiantRightColor":{"color":{"r":255,"g":255,"b":255,"a":1}},"stickerTextColor":{"color":{"r":12,"g":131,"b":207,"a":1}},"stickerActiveTextColor":{"color":{"r":244,"g":246,"b":248,"a":1}},"shapeTextColor":{"color":{"r":255,"g":255,"b":255,"a":1}},"shapeActiveTextColor":{"color":{"r":247,"g":171,"b":22,"a":1}},"circleGradiantLeftColor":{"color":{"r":16,"g":143,"b":234,"a":1}},"circleGradiantRightColor":{"color":{"r":19,"g":95,"b":163,"a":1}},"circleActiveGradiantLeftColor":{"color":{"r":239,"g":207,"b":16,"a":1}},
"circleActiveGradiantRightColor":{"color":{"r":249,"g":162,"b":23,"a":1}}}';
            $jsonPage = json_decode($defaultPage);
            $jsonPage->logo = isset($graphics->header_logo) ? $graphics->header_logo : "uploads/consultation-defaults/progress-speed.bfb7ea72.png";

            $data = [
                [
                    "setting_key"   => "email_for_invite_friends_EN",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "email_for_invite_friends_FR",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ], [
                    "setting_key"   => "email_for_late_participants_EN",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "email_for_late_participants_FR",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "reinvent_page",
                    "setting_value" => json_encode($jsonPage),
                ],
                [
                    "setting_key"   => "resilience_signin_page",
                    "setting_value" => ($this->getSignInData()),
                ],
                [
                    "setting_key"   => "resilience_signup_page",
                    "setting_value" => ($this->getSignUpData()),
                ], [
                    "setting_key"   => "resilience_verification_page",
                    "setting_value" => ($this->getVerificationData()),
                ],
                [
                    "setting_key"   => "resilience_forgot_page",
                    "setting_value" => ($this->getForgotData()),
                ],
                [
                    "setting_key"   => "consultation_reminder",
                    "setting_value" => '{"open_consultation":0, "late_participants":0, "reminders":{"reminder1":{"active":0,"days":0}, "reminder2":{"active":0,"days":0}, "reminder3":{"active":0,"days":0}, "reminder4":{"active":0,"days":0}}}',
                ],
                [
                    "setting_key"   => "email_for_consultation_EN",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "email_for_consultation_FR",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ],
                [
                    "setting_key"   => "resilience_validation_code_EN",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_code":"text_before_code","text_between_code_and_link":"text_between_code_and_link","text_after_link":"text_after_link"}',
                ],
                [
                    "setting_key"   => "resilience_validation_code_FR",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_code":"text_before_code","text_between_code_and_link":"text_between_code_and_link","text_after_link":"text_after_link"}',
                ],
                [
                    "setting_key"   => "resilience_reminder_1_EN",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ], [
                    "setting_key"   => "resilience_reminder_1_FR",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ], [
                    "setting_key"   => "resilience_reminder_2_EN",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ], [
                    "setting_key"   => "resilience_reminder_2_FR",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ], [
                    "setting_key"   => "resilience_reminder_3_EN",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ], [
                    "setting_key"   => "resilience_reminder_3_FR",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ], [
                    "setting_key"   => "resilience_reminder_4_EN",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ], [
                    "setting_key"   => "resilience_reminder_4_FR",
                    "setting_value" => '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}',
                ], [
                    "setting_key"   => "resilience_user_manual_option_EN",
                    "setting_value" => '{"email_subject":"A new option has been added to [[Sprint]] [[Step]]","text_before_link":"Sprint: [[Sprint]] <br/> Step: [[Step]] <br/> Question: [[Question]] <br/> New option: [[NewOption]] <br/> added by [[UserFirstName]] [[UserLastName]] on [[DateTime]]","text_after_link":"text_after_link\n"}',
                ], [
                    "setting_key"   => "resilience_user_manual_option_FR",
                    "setting_value" => '{"email_subject":"Un nouveau choix a été ajouté à [[Sprint]] [[Step]]","text_before_link":"Étape: [[Sprint]] <br/> Phase: [[Step]] <br/> Question: [[Question]] <br/> Nouveau choix: [[NewOption]] <br/> ajouté par [[UserFirstName]] [[UserLastName]] le [[DateTime]]","text_after_link":"text_after_link\n"}',
                ],
            ];
            $allowedToReplace = ["resilience_signin_page" => ['ORText'], 'resilience_signup_page' => ['signupButtonText']];

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
        }

        public function getSignInData()
        {
            $signIn = '{"ORText":"Or","forgetPasswordText":"forgetPasswordText","noAccountText":"noAccountText","signInExplanationText":"signInExplanationText","signInLeftPageDescription":"Lorsque l’ensemble des organisations professionnelles se mobilisent pour trouver les solutions à la crise, redevenir attractif pour nos adhérents et nos partenaires, redéfinir nos forces et ré-inventer les organisations professionnelles.

Travaillons entre Présidents, élus, délégués généraux, secrétaires généraux, communication- marketing, service aux adhérents, juridique, social, économique, technique, services aux adhérents,... puis tous ensemble, pour le CHALLENGE des Actions de RELANCE."
           ,"signInLeftPageTitleLine1":"Tous ensemble pour le CHALLENGE" 
           ,"signInLeftPageTitleLine2":"des Actions de RELANCE" 
           ,"signInWelcomeTextLine1":"Bienvenue au Think Tank des" 
           ,"signInWelcomeTextLine2":"organisations professionnelles" 
           ,"signInButtonText":"Sign In" 
           ,"signupText":"Signin text","logo":"uploads/consultation-defaults/progress-speed.bfb7ea72.png" ,
           "mainBackgroundColor":{"color":{"r":255,"g":255,"b":255,"a":1}}, "mainTextColor":{"color":{"r":0,"g":0,"b":0,"a":1}}
            ,"titleTextColor1":{"color":{"r":255,"g":255,"b":255,"a":1}}
           ,"titleTextColor2":{"color":{"r":175,"g":210,"b":249,"a":1}}
           ,"alternateColor":{"color":{"r":17,"g":146,"b":187,"a":1}}
            }';
            return $signIn;
        }

        public function getSignUpData()
        {
            $signUp = '{"signUpLeftPageDescription":"Lorsque l’ensemble des organisations professionnelles se mobilisent pour trouver les solutions à la crise, redevenir attractif pour nos adhérents et nos partenaires, redéfinir nos forces et ré-inventer les organisations professionnelles.

Travaillons entre Présidents, élus, délégués généraux, secrétaires généraux, communication- marketing, service aux adhérents, juridique, social, économique, technique, services aux adhérents,... puis tous ensemble, pour le CHALLENGE des Actions de RELANCE."
           ,"signUpLeftPageTitleLine1":"Tous ensemble pour le CHALLENGE" 
           ,"signUpLeftPageTitleLine2":"des Actions de RELANCE" 
           ,"signUpWelcomeTextLine1":"Bienvenue au Think Tank des" 
           ,"signUpWelcomeTextLine2":"organisations professionnelles" 
          ,"signUpExplanationText":"signUpExplanationText"
          ,"logo":"uploads/consultation-defaults/progress-speed.bfb7ea72.png",
          "FirstNameCaptionText":"First Name",
          "lastNameCaptionText":"Last Name",
          "UnionCaptionText":"Union",
          "PositionInUnionCaptionText":"Position in Union",
          "CompanyCaptionText":"Company",
          "PositionInCompanyCaptionText":"Position in Company",
          "OtherCaptionPosition":"Other",
          "SpecificFieldsZone1":"union",
          "HasAccountText":"HasAccount",
          "SignInText":"SignIn",
           "mainBackgroundColor":{"color":{"r":255,"g":255,"b":255,"a":1}}, "mainTextColor":{"color":{"r":0,"g":0,"b":0,"a":1}}
            ,"titleTextColor1":{"color":{"r":255,"g":255,"b":255,"a":1}}
           ,"titleTextColor2":{"color":{"r":175,"g":210,"b":249,"a":1}}
           ,"alternateColor":{"color":{"r":17,"g":146,"b":187,"a":1}}
           ,"signupButtonText":"Inscrivez-vous"
            }';
            return $signUp;
        }

        public function getVerificationData()
        {
            $verification = '{
           "VerificationCodeWelcomeTextLine1":"Bienvenue au Think Tank des" 
           ,"VerificationCodeWelcomeTextLine2":"organisations professionnelles" 
          ,"VerificationCodeButtonText":"Proceed",
          "VerificationCodeCaptionText":"Email",
          "VerificationCodePageTextLine1":"Tous ensemble pour le CHALLENGE",
          "VerificationCodePageTextLine2":"des Actions de RELANCE",
          "VerificationCodePageText":"Lorsque l’ensemble des organisations professionnelles se mobilisent pour trouver les solutions à la crise, redevenir attractif pour nos adhérents et nos partenaires, redéfinir nos forces et ré-inventer les organisations professionnelles.
          
          Travaillons entre Présidents, élus, délégués généraux, secrétaires généraux, communication- marketing, service aux adhérents, juridique, social, économique, technique, services aux adhérents,... puis tous ensemble, pour le CHALLENGE des Actions de RELANCE."
            }';
            return $verification;
        }

        public function getForgotData()
        {
            $forgot = '{
           "ForgotWelcomeTextLine1":"Bienvenue au Think Tank des" 
           ,"ForgotWelcomeTextLine2":"organisations professionnelles" 
          ,"ForgetPasswordButtonText":"Continuer",
          "ForgotCaptionText":"Email",
          "ForgotPageTextLine1":"Tous ensemble pour le CHALLENGE",
          "ForgotPageTextLine2":"des Actions de RELANCE",
          "ForgotPageText":"Lorsque l’ensemble des organisations professionnelles se mobilisent pour trouver les solutions à la crise, redevenir attractif pour nos adhérents et nos partenaires, redéfinir nos forces et ré-inventer les organisations professionnelles.
          
          Travaillons entre Présidents, élus, délégués généraux, secrétaires généraux, communication- marketing, service aux adhérents, juridique, social, économique, technique, services aux adhérents,... puis tous ensemble, pour le CHALLENGE des Actions de RELANCE."
            }';
            return $forgot;
        }
    }