<?php

return [
    'dateFormat'    => 'Y-m-d H:i:s',
    'event_default_image_path'  => 'general/event_image/default.jpg',
    'group_logo_default_image'  => 'general/group_logo/default.png',
    'storage_paths' => [
        'event_image'                       => 'events/image',
        'group_logo'                        => 'groups/logo',
        'business_team_icon'                => 'label_icons/business_team_icon',
        'business_team_altImage'            => 'label_icons/business_team_altImage',
        'vip_icon'                          => 'label_icons/vip_icon',
        'vip_altImage'                      => 'label_icons/vip_altImage',
        'moderator_icon'                    => 'label_icons/moderator_icon',
        'expert_icon'                       => 'label_icons/expert_icon',
        'expert_altImage'                   => 'label_icons/expert_altImage',
        'video_explainer_alternative_image' => 'groups/explainer_altImage',
        'live_event_image'                  => 'event_live_images',
        'live_event_video_thumbnails'       => 'live_event_video_thumbnails',
    ],
    'email_keys'    => [
        'email_validation_code' => [
            'name' => 'email_validation_code',
            'link' => '/quick-login/:EVENT_UUID',
            'tags' => [],
        ],
    ],
    'setting_keys'  => [
        // email keys
        'email_validation_code'     => 'email_validation_code',
        // group graphics keys
        'group_logo'                => 'group_logo',
        'group_main_colors'         => 'group_main_colors',
        'group_email_banners'       => 'group_email_banners',
        'group_header_footer'       => 'group_header_footer',
        'group_space_settings'      => 'group_space_settings',
        'group_texture_settings'    => 'group_texture_settings',
        'group_colors'              => 'group_colors',
        'group_tag_colors'          => 'group_tag_colors',
        'group_registration_colors' => 'group_registration_colors',
    ],
    's3'            => [
        'event_image' => 'group/event_default_image',
        'group_logo'  => 'group/group_logo',
    ],
    'icons'         => [
        'business_team_icon'     => 'business_team_icon',
        'business_team_altImage' => 'business_team_altImage',
        'vip_icon'               => 'vip_icon',
        'vip_altImage'           => 'vip_altImage',
        'moderator_icon'         => 'moderator_icon',
        'expert_icon'            => 'expert_icon',
        'expert_altImage'        => 'expert_altImage',
    ],
    'reservedJoinCode' => [
        'water-fountain',
    ]
];
