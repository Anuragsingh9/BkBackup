<?php

return [
    'otp_resend_sec'                => 60,
    'conference_type'               => [
        'zoom' => 1,
        'bj'   => 2,
    ],
    'setting_keys'                  => [
        'account_settings'    => [
            'events_enabled'               => 0,
            'kct_enabled'                  => 1,
            'conference_enabled'           => 0,
            // new keys
            'allow_multi_group'            => 0,
            'max_group_limit'              => 1,
            'allow_user_to_group_creation' => 0,
            'group_analytics'              => 0,
            'event_analytics'              => 0,
            'acc_analytics'                => 1,
            'all_day_event_enabled'        => 1, // water fountain event enabled
        ],
        'conference_settings' => [
            'current_conference' => 1, // 1 For Zoom, @see above `conference_type`
            'bluejeans'          => [
                'app_key'           => null,
                'app_secret'        => null,
                'app_email'         => null,
                'number_of_license' => 0,
            ],
            'zoom'               => [
                'app_key'           => null,
                'app_secret'        => null,
                'app_email'         => null,
                'number_of_license' => 0,
            ],
        ],
    ],
    'tagExportHeader'               => [
        'created_at' => 'Created At',
    ],
    'filePaths'                     => [
        'emailHeaderLogo' => 'email_header.png',
        'emailFooterLogo' => 'email_footer.png',
    ],
    's3'                            => [
        'video_explainer_image' => '/assets/video_explainer_alt',
    ],
    'video_explainer_default_image' => 'assets/default-video-explainer-alt.png',
    'default_group_key'             => 'default',
];
