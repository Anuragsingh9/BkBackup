<?php

return [
    'name'                    => 'Events',
    'defaults'                => [
        'keepContact'      => [
            'keepContact' => [
                'page_customisation' => [
                    'keepContact_page_title'       => 'Keep Contact Platform',
                    'keepContact_page_description' => 'Keep Contact Platform Description',
                    'keepContact_page_logo'        => '',
                    'website_page_link'            => '',
                    'twitter_page_link'            => '',
                    'linkedIn_page_link'           => '',
                    'facebook_page_link'           => '',
                    'instagram_page_link'          => '',
                ],
                'graphics_setting'   => [
                    'main_background_color'                  => ['color' => ['r' => 255, 'g' => 255, 'b' => 255, 'a' => 1]],
                    'texts_color'                            => ['color' => ['r' => 0, 'g' => 0, 'b' => 0, 'a' => 1]],
                    'keepContact_color_1'                    => ['color' => ['r' => 249, 'g' => 249, 'b' => 249, 'a' => 1]], // main color 1
                    'keepContact_color_2'                    => ['color' => ['r' => 255, 'g' => 255, 'b' => 255, 'a' => 1]], // main color 2
                    'keepContact_background_color_1'         => ['color' => ['r' => 255, 'g' => 255, 'b' => 255, 'a' => 1]], // f9f9f9
                    'keepContact_background_color_2'         => ['color' => ['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0.8]], // main color 2 , 80%
                    'keepContact_selected_space_color'       => ['color' => ['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0.2]], // main color 2 , 20%
                    'keepContact_unselected_space_color'     => ['color' => ['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0.5]], // main color 2 , 50%
                    'keepContact_closed_space_color'         => ['color' => ['r' => 215, 'g' => 215, 'b' => 215, 'a' => 1]], // D7D7D7
                    'keepContact_text_space_color'           => ['color' => ['r' => 255, 'g' => 255, 'b' => 255, 'a' => 1]], // ffffff
                    'keepContact_names_color'                => ['color' => ['r' => 112, 'g' => 109, 'b' => 109, 'a' => 1]], // 70gdgd
                    'keepContact_thumbnail_color'            => ['color' => ['r' => 112, 'g' => 109, 'b' => 109, 'a' => 1]], // 70gdgd
                    'keepContact_countdown_background_color' => ['color' => ['r' => 255, 'g' => 192, 'b' => 0, 'a' => 1]], // ffc000
                    'keepContact_countdown_text_color'       => ['color' => ['r' => 255, 'g' => 255, 'b' => 255, 'a' => 1]], // ffffff
                    "hover_border_color"                     => ['color' => ['r' => 105, 'g' => 188, 'b' => 247, 'a' => 1]],
                ],
                'section_text'       => [
                    'reply_text'                => 'REPLAY & CONTENUS DISPONIBLES',
                    'keepContact_section_line1' => 'Keep Contact Title 1',
                    'keepContact_section_line2' => 'Keep Contact Title 1',
                ]
            ],
        ],
        'manual_opening'   => 0,
        'event_list_order' => 'date',
    ],
    'validations'             => [
        'default_min'              => 3,
        'title'                    => 100,
        'header'                   => 250,
        'line_one'                 => 44,
        'line_two'                 => 56,
        'description'              => 250,
        'address'                  => 500,
        'city'                     => 250,
        'image_w'                  => 100, // pixel
        'image_h'                  => 70, // pixel
        'image_width_height_ratio' => 2,
        'image_height_width_ratio' => 2,
        'manual_opening_possible'  => 900 // in seconds
    ],
    'event_type'              => [
        'int'     => 'int',
        'ext'     => 'ext',
        'virtual' => 'virtual',
    ],
    'reminder_enabled_domain' => [1, 56, 40],
];
