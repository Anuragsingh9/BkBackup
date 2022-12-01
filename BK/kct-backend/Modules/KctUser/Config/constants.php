<?php

return [
    'setting_keys' => [
        'validation_code' => 'event_validation_code',
        'email_graphics'      => 'email_graphics',
    ],

    'space_image_system'       => 1,
    'space_image_stock'        => 2,
    'lexo_rank_min'            => 'a', // this rank will not be allocated to any order id
    'lexo_rank_max'            => 'z', // this rank will not be allocated to any order id
    'space_start_order'        => 'b',
    'aws_meeting_expired_code' => 'NotFoundException',
    'user_side_date_format'    => 'H:i:s',
    'bj_options'               => [
        // all these options must be 1 0 only as validation class will put validation of 1 or 0 to all these
        'event_chat', 'attendee_search', 'q_a', 'allow_anonymous_questions', 'auto_approve_questions',
        'auto_recording', 'phone_dial_in', 'raise_hand', 'display_attendee_count', 'allow_embedded_replay',
    ],
    'zoom_options'             => [
        // all these options must be 1 0 only as validation class will put validation of 1 or 0 to all these
        'video_hosts_activated', 'video_panelist_activated', 'q_a', 'enable_practise_session', 'auto_recording',
    ],
    'conference_type'          => [
        'bj'   => 'bj',
        'zoom' => 'zoom',
    ],
    'zoom'                     => [
        "base_url"                                   => "https://api.zoom.us/v2/",
        "create_webinar"                             => "users/userId/webinars",
        "get_update_delete_webinar"                  => "webinars/webinarId",
        "create_update_get_remove_webinar_panelists" => "webinars/webinarId/panelists",
        "remove_webinar_panelist"                    => "webinars/webinarId/panelists/panelistId",
        "create_user"                                => "users",
        "get_update_delete_user"                     => "users/userId",
        "add_registrant"                             => "/webinars/webinarId/registrants"
    ],
    'conference_time_block'    => [
        'before' => 1,
        'during' => 2,
        'after'  => 3,
    ]

];
