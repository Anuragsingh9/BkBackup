<?php

return [
    'moduleLanguages'          => [
        'en' => 'en',
        'fr' => 'fr',
    ],
    'default_lang'             => 'en',
    'name'                     => 'KctUser',
    'token_name'               => 'keep_contact',
    's3'                       => [
        'space_image'           => 'keep_contact/event/event_uuid/space/image',
        'space_icon'            => 'keep_contact/event/event_uuid/space/icon',
        'graphic_logo'          => 'keep_contact/event/event_uuid/graphics',
        'user_avatar'           => 'uploads/user_profile',
        'default_graphics_logo' => 'keep_contact/graphics_logo',
        'v2_event_default_img'  => 'event/event_default_image.jpeg',
    ],
    'pagination'               => [
        'event_participants' => 10
    ],
    'validations'              => [
        'default_min'  => 3,
        'space'        => [
            'name_max'                 => '100',
            'short_name_max'           => '100',
            'mood_max'                 => '500',
            'image_w'                  => 100, // pixel
            'image_h'                  => 70, // pixel
            'image_width_height_ratio' => 2,
            'image_height_width_ratio' => 2,
            'opening_hour_before_max'  => 120,
            'opening_hour_after_max'   => 120,
            'max_capacity_max'         => 250,
            'max_capacity_max_v2'      => 144,
            'space_line_1'             => 14,
            'space_line_2'             => 14,
            'space_max_hosts'          => 1,
            'space_min_hosts'          => 1,
            'event_max_hosts'          => 1,
            'event_min_hosts'          => 1,
        ],
        'kct'          => [
            'page_title'       => '100',
            'page_description' => '100',
            'section_line1'    => '100',
            'section_line2'    => '100',
            'reply_text'       => '100',
        ],
        'bluejeans'    => [
            'event_name_min' => '10',
            'event_name_max' => '40',
        ],
        'registration' => [
            'title_max'  => '100',
            'points_max' => '500',
        ],

        'entity' => [
            'long_name_max'  => 100,
            'long_name_min'  => 3,
            'short_name_max' => 3,
            'short_name_min' => 100,
        ]
    ],
    'embedded_url_show_before' => 900, // 15 min
    'embedded_url_template'    => 'https://primetime.bluejeans.com/a2m/embeds/attendee?sharingID=[[SHARING_ID]]&captureUserDetails=false&applyCustomization=true&launchFullExperience=false',
    'space_max_hosts'          => 2,
    'space_default_short_name' => 'DEFAULT',
    'space_default_mood'       => 'Default',
    'default'                  => [
        'opening_before'           => 15,
        'opening_after'            => 90,
        'presence_status'          => 'ANE',
        'front_domain'             => 'keepcontact.events',
        'kct_logo'                 => 'event/event_default_logo.png',
        'is_presenter'             => 0,
        'is_moderator'             => 0,
        'lang'                     => 'FR',
        'union_member_type'        => 0,
        'ops_logo'                 => 'opsimplify-logo.jpg',
        'space_type_vip'           => 1,
        'space_type_duo'           => 2,
        'v2_space_max_capacity'    => 144, // as the new capacity vo v2 is introduced
        'v2_opening_hour'          => [ // the default opening hours values for the space and event in v2
            'after'  => 0,
            'before' => 0,
            'during' => 1,
        ],
        'custom_graphics'          => [
            'colors'     => [
                // has custom background section
                'background_color'       => ['r' => 255, 'g' => 255, 'b' => 255, 'a' => 1], // if off send FFFFFF
                'separation_line_color'  => ['r' => 235, 'g' => 240, 'b' => 244, 'a' => 1], // if off send EBF0F4
                'text_color'             => ['r' => 255, 'g' => 255, 'b' => 255, 'a' => 1], // if off send accountcolor2
                // customized color
                'event_color_1'          => ['r' => 255, 'g' => 255, 'b' => 255, 'a' => 1], // if off send account color 1
                'event_color_2'          => ['r' => 255, 'g' => 255, 'b' => 255, 'a' => 1], // if off send account color 2
                'event_color_3'          => ['r' => 255, 'g' => 255, 'b' => 255, 'a' => 1], // if off send account color 2
                // bottom bg color
                'bottom_bg_color'        => ['r' => 255, 'g' => 255, 'b' => 255, 'a' => 1], // if off send this
                'tag_color'              => ['r' => 255, 'g' => 255, 'b' => 255, 'a' => 1],
                //badge and qss join button
                'badge_bg_color'         => ['r' => 255, 'g' => 255, 'b' => 255, 'a' => 1], // if off send this
                'join_bg_color'          => ['r' => 255, 'g' => 255, 'b' => 255, 'a' => 1], // if off send this
                'join_text_color'        => ['r' => 255, 'g' => 255, 'b' => 255, 'a' => 1], // if off send accountcolor2
                //user tags default color
                'professional_tag_color' => ['r' => 53, 'g' => 173, 'b' => 129, 'a' => 1],  // if off send this
                'personal_tag_color'     => ['r' => 109, 'g' => 53, 'b' => 173, 'a' => 1],  // if off send this
            ],
            'checkboxes' => [
                // has custom background section
                'has_custom_background'    => 0, // default off
                // customized texture section
                'customized_texture'       => 0, // default off
                'texture_square_corner'    => 0, // default off as parent is enabled = 0
                'texture_remove_frame'     => 0, // default off as parent is enabled = 0
                'texture_remove_shadow'    => 0, // default off as parent is enabled = 0
                // customized color
                'customized_colors'        => 0, // default off
                'unselected_spaces_square' => 0, // default off means rounded
                'selected_spaces_square'   => 0, // default off means rounded
                // bottom bg color
                'bottom_bg_is_colored'     => 0, // default off
                // label customization status
                'label_customization'      => 1, // default off
            ],
            'urls'       => [
                'video_url' => 'https://www.youtube.com/watch?v=VnyitUU4DUY',
            ],
            'label'      => [
                'host_label'      => ['en' => 'Host', 'fr' => 'Host'],
                'presenter_label' => ['en' => 'Presentor', 'fr' => 'Présentateur'],
                'moderator_label' => ['en' => 'Moderator', 'fr' => 'Modérateur'],
                'team_label'      => ['en' => 'Team', 'fr' => 'Team'],
                'expert_label'    => ['en' => 'Expert', 'fr' => 'Expert'],
            ],
            'number'     => [
                'max_conversation_users' => 4,
            ],
        ],
        'kct_event_default_colors' => [
            'event_color_1' => ['r' => 238, 'g' => 204, 'b' => 71, 'a' => 1], // if off send account color 2
            'event_color_2' => ['r' => 30, 'g' => 77, 'b' => 125, 'a' => 1], // if off send account color 1
            'event_color_3' => ['r' => 59, 'g' => 109, 'b' => 169, 'a' => 1], // if off send account color 2
        ]
    ],
    'bluejeans'                => [
        'base_url'        => 'https://a2m.bluejeans.com',
        'access_token'    => '/api/security/v1/oauth/accesstoken?User',
        'create_event'    => '/api/scheduling/v1/users/userId/events',
        'update_event'    => '/api/scheduling/v1/users/userId/events/eventId',
        'delete_event'    => '/api/scheduling/v1/users/userId/events/eventId',
        'add_member'      => '/api/scheduling/v1/users/userId/events/eventId/invite',
        'get_event'       => '/api/scheduling/v1/users/userId/events/eventId',
        'timezone'        => 'Europe/Paris',
        'account_sign_up' => 'https://www.bluejeans.com/',
    ],
    'view'                     => [ // email template for each event
        'dynamic' => 'email_template.event_email_dynamic_template',
    ],
    'registration_link'        => 'https://:domain/register/:eventUuid',
    'available_lang'           => [
        'EN', 'FR',
    ],
    'event_time_db_format'     => 'H:i:s',
    'setting_keys'             => [
        'event_register'            => 'event_register',
        'magic_link'                => 'event_magic_link',
        'validation_code'           => 'event_validation_code',
        'reminder_settings'         => "event_reminders",
        'reminder_mails'            => [
            'event_int_reminder_1' => 'event_int_reminder_1',
            'event_int_reminder_2' => 'event_int_reminder_2',
            'event_int_reminder_3' => 'event_int_reminder_3',
            'event_kct_reminder_1' => 'event_kct_reminder_1',
            'event_kct_reminder_2' => 'event_kct_reminder_2',
            'event_kct_reminder_3' => 'event_kct_reminder_3',
        ],
        'virtual_event_emails_key'  => [
            'event_kct_reminder_1',
            'event_kct_reminder_2',
            'event_kct_reminder_3',
        ],
        'internal_event_emails_key' => [
            'event_int_reminder_1',
            'event_int_reminder_2',
            'event_int_reminder_3',
        ],
        'event_custom_graphics'     => 'event_custom_graphics',
    ],
    'links'                    => [
        'event_join_link'     => ':domain/j/:event_uuid',
        'event_reg_link'      => ':domain/register/:event_uuid',
        'kct_event_reg_link'  => ':domain/quick-register/:event_uuid',
        'kct_event_join_link' => ':domain/quick-login/:event_uuid',
    ],
    'events_name'              => [
        'manuallyOpenEvent'        => 'manuallyOpenEvent',
        'eventEndUpdated'          => 'eventEndUpdated',
        'conversationDeleted'      => 'conversationDeleted',
        'conversationLeave'      => 'conversationLeave',
        'momentStatusUpdated'      => 'conferenceUpdated',
        'zoomMeetingStatusUpdated' => 'zoomMeetingStatusUpdated',
        'eventDataUpdated'         => 'eventDataUpdated',
        'eventReset'               => 'eventRest',
    ],
    'dummy_event'              => [
        'dummy_space' => [
            'numberOfUsers'      => 50,
            'dummy_spaces_count' => 3,
        ]
    ],
    'front_path'               => [
        'email_verify'    => 'quick-otp/EVENT_UUID',
        'quick_login'     => 'quick-login/EVENT_UUID',
        'quick_user_info' => 'quick-user-info/EVENT_UUID',
        'event_list'      => 'event-list',
        'event_register'  => 'quick-register/EVENT_UUID',
    ],
    'user_ban'                 => [
        'severity' => [1, 2, 3]
    ],
    'dummy_space_host_emails'  => [
        'johanna.martines@internetbusinessbooster.com', 'peter.schmitt@internetbusinessbooster.com',
    ],

    'event_type' => [
        'int'     => 'int',
        'ext'     => 'ext',
        'virtual' => 'virtual',
    ],
];
