<?php

return [
    'default_text_min' => 2,
    'users'            => [
        'validations' => [
            'fname_min'       => 2,
            'fname_max'       => 200,
            'lname_min'       => 2,
            'lname_max'       => 200,
            'password_min'    => 8,
            'password_max'    => 32,
            'bulk_insert_max' => 500,
            'bulk_delete_max' => 500,
        ],
    ],
    'events'           => [
        'validations' => [
            'title_min'     => 3,
            'title_max'     => 100,
            'header1_max'   => 44,
            'header2_max'   => 56,
            'header_max'    => 1100,
            'image_w'       => 100, // pixel
            'image_h'       => 70, // pixel
            'join_code_min' => 3,
            'join_code_max' => 20,
        ],
    ],
    'spaces' => [
        'defaults'    => [
            'max_capacity'     => 1000, // max capacity a space can have
            'min_capacity'     => 12, // min capacity a space can have
            'start_order'      => 'b', // start order is b so space can come between a and b order e.g. aa, ab, aaa, aab
            'default_capacity' => 144, //default capacity of space
            'default_min'      => 3,
        ],
        'values'      => [
            'space_type_vip' => 1,
        ],
        'validations' => [
            'space_line_1'    => 14,
            'space_line_2'    => 14,
            'space_max_hosts' => 1,
            'space_min_hosts' => 1,
        ]
    ],
    'groups' => [
        'validations' => [
            'min_name'        => 3,
            'max_name'        => 30,
            'min_group_key'   => 3,
            'max_group_key'   => 10,
            'max_description' => 500,
            'max_type_value'  => 100,
        ]
    ],
    'event_live_image' => [
        'max_width'            => 2400,
        'max_height'           => 1350,
        'max_image_limit'      => 5,
        'max_video_limit'      => 5,
        'thumbnail_max_width'  => 265,
        'thumbnail_max_height' => 150,
    ],
    'event_recurrence' => [
        'daily'     => 1,
        'weekdays'  => 2,
        'weekly'    => 3,
        'bimonthly' => 4,
        'monthly'   => 5
    ]
];
