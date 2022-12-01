<?php

return [
    'name' => 'Newsletter',
    'SENDER_LIST_PAGINATION_NUMBER' => 10,
    'TEMPLATE_LIST_PAGINATION_NUMBER' => 10,
    'NEWSLETTER_LIST_PAGINATION_NUMBER' => 10,

    'providers' => [
        Modules\Newsletter\Repositories\NewsletterRepoServiceProvide::class
    ]
];
