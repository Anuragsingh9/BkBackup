<?php

return [
    'name'                              => 'Newsletter',
    'SENDER_LIST_PAGINATION_NUMBER'     => 10,
    'TEMPLATE_LIST_PAGINATION_NUMBER'   => 10,
    'NEWSLETTER_LIST_PAGINATION_NUMBER' => 10,
    
    'providers'   => [
        Modules\Newsletter\Repositories\NewsletterRepoServiceProvide::class
    ],
    'limit'       => [
        'title'       => 65,
        'header'      => 1000,
        'description' => 3000,
    ],
    'validations' => [
        'news' => [
            'title'       => '30',
            'header'      => '30',
            'description' => '50',
        ],
    ],
    's3'          => [
        'news_image' => 'news/image',
    ],
];
