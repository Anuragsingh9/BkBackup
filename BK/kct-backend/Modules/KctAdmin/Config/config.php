<?php

return [
    'moduleLanguages'         => [
        'en' => 'en',
        'fr' => 'fr',
    ],
    'default_lang'            => 'en',
    'name'                    => 'KctAdmin',
    'default'                 => [
        'group_settings' => [
            'colors'     => [
                // quick design settings
                'main_color_1'                 => '#0589B8', // main color 1
                'main_color_2'                 => '#3B3B3B', // main color 2
                // headers section
                'header_bg_color_1'            => '#FFFFFF',
                'header_separation_line_color' => '#E7E7E7',
                'header_text_color'            => '#3B3B3B',
                // button customization section
                'customized_join_button_bg'    => '#4190B6',
                'customized_join_button_text'  => '#FFFFFF',
                // space host section
                'sh_background'                => '#3B3B3B',
                // conversation section
                'conv_background'              => '#3B3B3B',
                // user badge section
                'badge_background'             => '#FFFFFF',
                // space section
                'space_background'             => '#3B3B3BBF',
                // user grid
                'user_grid_background'         => '#3B3B3B',
                'user_grid_pagination_color'   => '#0589B8',
                // tags customization
                'event_tag_color'              => '#0589B8',
                'professional_tag_color'       => '#6D35AD',
                'personal_tag_color'           => '#35AD81',
                'tags_text_color'              => '#FFFFFF',
                // content customization
                'content_background'           => '#3B3B3B',
            ],
            'checkboxes' => [
                // quick design settings
                'apply_customisation'      => 0,
                // headers section
                'header_footer_customized' => 0,
                // button customization section
                'button_customized'        => 0,
                // texture section
                'texture_customized'       => 0,
                'texture_square_corners'   => 0,
                'texture_remove_frame'     => 0,
                'texture_remove_shadows'   => 0,
                // space host customisation
                'sh_customized'            => 0,
                'sh_hide_on_off'           => 0,
                // conversation customisation
                'conv_customization'       => 0,
                // user badge section
                'badge_customization'      => 0,
                // space section
                'space_customization'      => 0,
                'unselected_spaces_square' => 0,
                'selected_spaces_square'   => 0,
                'extends_color_user_guide' => 0,
                // user grid
                'user_grid_customization'  => 0,
                // tags customization
                'tags_customization'       => 0,
                // content customization section
                'content_customized'       => 0,
                // label customization
                'label_customized'         => 0,
                //general setting
                'general_setting'          => 0,
                //Invite attendee
                'invite_attendee'          => 0,
                //video explainer
                'video_explainer'          => 1,
                // allow group customisation
                'group_has_own_customization' => 0,
            ],
            'textBoxes'  => [
                // headers section
                'header_line_1' => null,
                'header_line_2' => null,
                'qss_video_url' => 'https://www.youtube.com/watch?v=VnyitUU4DUY',
            ],
            'images'     => [
                //we also update the path in constants file
                // link is for default image temporarily
                // quick design settings
                'event_image'            => null,
                'group_logo'             => null,
                'business_team_icon'     => null,
                'business_team_altImage' => null,
                'vip_icon'               => null,
                'vip_altImage'           => null,
                'moderator_icon'         => null,
                'expert_icon'            => null,
                'expert_altImage'        => null,
                'video_explainer_alternative_image' => null,
            ],
            'arrays'     => [
            ],
        ],
    ],
    'hct_oit_graphic_aliases' => [
        // previous keys -----------> new keys (current system)
        'background_color'         => 'header_bg_color_1',
        'separation_line_color'    => 'header_separation_line_color',
        'text_color'               => 'header_text_color',
        'event_color_1'            => 'main_color_1',
        'event_color_2'            => 'main_color_2',
        'event_color_3'            => 'main_color_1',
        'tag_color'                => 'event_tag_color',
        'join_bg_color'            => 'customized_join_button_bg',
        'join_text_color'          => 'customized_join_button_text',
        'professional_tag_color'   => 'professional_tag_color',
        'personal_tag_color'       => 'personal_tag_color',
        'has_custom_background'    => 'header_footer_customized',
        'customized_texture'       => 'texture_customized',
        'texture_square_corner'    => 'texture_square_corners',
        'texture_remove_frame'     => 'texture_remove_frame',
        'texture_remove_shadow'    => 'texture_remove_shadows',
        'customized_colors'        => 'apply_customisation',
        'unselected_spaces_square' => 'unselected_spaces_square',
        'selected_spaces_square'   => 'selected_spaces_square',
        'label_customization'      => 'header_footer_customized',
        'kct_graphics_logo'        => 'group_logo',
        'kct_graphics_color1'      => 'main_color_1',
        'kct_graphics_color2'      => 'main_color_2',
        'video_url'                => 'qss_video_url',
    ],
    'user_import_heading'     => [
        "fname",
        "lname",
        "email",
        "city",
        "country",
        "address",
        "postal",
        "phone_number",
        "mobile_number",
        "company",
        "company_position",
        "union",
        "union_position",
        "internal_id",
    ],
    'zoom_oauth_url'          => 'https://zoom.us/oauth/authorize',
    'broadcast_keys'          => [
        'default_zoom_settings' => [
            'enabled'      => 0,
            'is_assigned'  => 0,
            'webinar_data' => [],
            'meeting_data' => [],
            'account_id'   => null,
        ],
        'custom_zoom_settings'  => [
            'enabled'      => 0,
            'is_assigned'  => 0,
            'webinar_data' => [],
            'meeting_data' => [],
            'token_data'   => [],
            'account_id'   => null,
        ],
    ],
    'scenery' => [
        'top_bg_color' => '#FFFFFF',
    ],
    'event_front_prefix' => [
        'recurring'    => 'j',
        'no_recurring' => 'e'
    ],
    'api_custom_code' => [
        'invalid_group' => 1001,
        'user_email_not_verified' => 1002,
    ]
];
