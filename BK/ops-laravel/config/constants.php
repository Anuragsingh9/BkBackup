<?php
    return [
        'WHITE_LIST_DOMAIN'         => [
            'https://projectdevzone.com/',
            'http://sharabh.ooionline.com:3000',
            'https://sharabh.ooionline.com:3000',
            'https://localhost:3000',
            'http://localhost:3000',
            'https://projectdevzone.com',
            'http://projectdevzone.com',
            'http://*.ooionline.com',
            'https://*.ooionline.com',
            'https://cartetppro.fr',
            'http://cartetppro.fr',
            'https://cartetppro.fr',
            'https://opsimplify.app',
            'https://sharabh.re-invent.solutions',
            'https://*.re-invent.solutions',
            'http://*.re-invent.solutions',
            'https://*.keepcontact.events', // keep contact
        ],
        'WHITE_LIST_DOMAIN_PATTERN' => [    // the regex of allowed domains
            // example => https | sub domain required | domain from env
            '(^https:\/\/(\w+\.)(keepcontact).events)', // https | subdomain required | keepcontact.events
            '(^https:\/\/(\w+\.)(re-invent).solutions)', // https | subdomain required | re-invent.solutions
//            '(^https:\/\/(\w+\.)('. env('HOST_SUFFIX') .')', // for all domain of respective host ooi , pas , ops

        ],
        'ICONTACT_ACCOUNT_ID'       => '',
        'ICONTACT_CLIENT_FOLDER_ID' => '',
        'ICONTACT_API_APP_ID'       => '',
        'ICONTACT_API_USERNAME'     => '',
        'ICONTACT_API_PASSWORD'     => '',
        "NEWSLETTER"                => 0,
        "CRM"                       => 0,
        'email_config'              => [
            'from_email'  => 'opsimplify@opsimplify.com',
            'from_name'   => 'PaSimplify',
            'reply_to'    => 'noreply@opsimplify.com',
            'host'        => 'email-smtp.eu-west-1.amazonaws.com',
            'username'    => 'AKIAIGPM3QDJTZPBMZBQ',
            'password'    => 'Atyh9t/FZIpj7QvqpDuqe9l5ZRWAHJGHFedAgIn0qchi',
            'smtp_secure' => 'ssl',
            'smtp_port'   => 465,
        ],

        'HOST_TYPE'     => 'http://',
        'HOST_SUFFIX'   => 'pasimplify.com',
        'EMAIL_SUFFIX'  => 'pasimplify.com',
        'REACT_APP_URL' => 'http://localhost:3000/#/',

        'AWS_PATH'   => 'https://s3-eu-west-2.amazonaws.com/pasimplify.com/',
        'AWS_KEY'    => 'AKIAJTVILNXNRJAH7RKQ',
        'AWS_SECRET' => '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',
        'AWS_REGION' => 'eu-west-2',
        'AWS_BUCKET' => 'pasimplify.com',

        // 'AWS_PATH'=>'https://s3.ap-south-1.amazonaws.com/ops.sharabh.org/',
        // 'AWS_KEY'=>'AKIAJVCBWV4BC3YGJCTQ',
        // 'AWS_SECRET'=>'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',
        // 'AWS_REGION'=>'ap-south-1',
        // 'AWS_BUCKET'=>'ops.sharabh.org',

        'qual_reminder_day1'                => 0,
        'qual_reminder_day2'                => 3,
        'qual_reminder_time1'               => '11:00',
        'qual_reminder_time2'               => '11:05',
        'qual_cron_day'                     => 1,
        'qual_cron_time'                    => '08:00',
        'FLASH_INVALID_CREDENTIAL'          => 'Username/Password does not macth!',
        'FLASH_INVALID_EMAIL_ADDRESS'       => 'Email address invalid!',
        'FLASH_EMAIL_ADDRESS_EXIST'         => 'Email address already exist!',
        'FLASH_RESET_PASS_LINK_SEND'        => 'Reset password link send successfully your mailbox. If the email takes more than 15 minutes to appear in your mailbox, please check your spam folder.',
        'FLASH_RESET_PASS_LINK_SEND_FAIL'   => 'Reset password link send failed. Try again!',
        'FLASH_RESET_PASS_SUCCESS'          => 'Your password successfully changed ! Login to continue!',
        'FLASH_RESET_PASS_FAIL'             => '404 Not Found or Reset password link expired !',
        'FLASH_ALL_FIELD_REQUIRED'          => 'All Fields are required !',
        'FLASH_NPASS_CPASS_NOT_MATCH'       => 'New password or Confirm password does not matched !',
        'FLASH_CPASS_LENGTH'                => "Password Must be 6 Character Long !",
        'FLASH_VERIFICATION_CODE_SEND'      => "Veuillez saisir le code de vérification qui vient d'être envoyé dans votre email.",
        'FLASH_VERIFICATION_CODE_SEND_FAIL' => 'Verification code send failed. Try again!',
//================
        'REJECTED_STATUS'                   => 5,
        'stepCount'                         => 2,
        'DEFAULT_LANG'                      => 'FR',
        'Today_DATE'                        => (\Carbon\Carbon::today()),
        'meeting_dates_limit'               => 3,
        'IM'                                => [
            'wordLimits' => [
                'channel_name'  => 1000,
                'topic_name'    => 1000,
                'message_reply' => 1000,
                'message_text'  => 1000,
            ],
        ],
        'repd_upcoming_limit' => 3,
        'defaults' => [
            's3' => [
                'notification-allow-guide-EN' => [
                    'name' => 'Browser Notification Enable Guide',
                    'path' => 'guides/EN/guide-default-en-notification-enable.pdf',
                ],
                'notification-allow-guide-FR' => [
                    'name' => "Guide d'activation de la notification du navigateur",
                    'path' => 'guides/FR/guide-default-fr-notification-enable.pdf'
                ],
            ],

            'organisation' => [
                'address1'    => ("55 Rue du Faubourg Saint-Honorè"),
                'address2'    => '',
                'postal_code' => '75008',
                'city'        => 'Paris',
                'country'     => 'France',
            ]
            // the default values
        ],
        'api_version' => [
            'cocktail' => 2,
            'events' => 3,
        ],
        // hostname hash code length
        'hostname_code_length' => 3,
        'dummy_users_bucket' => env('DUMMY_VIDEOS_BUCKET', 'kct-videos'),
    ];



