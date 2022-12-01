<?php

return [
    'default_text_min' => 2,
    'users'            => [
        'validations' => [
            'fname_min'       => 2,
            'fname_max'       => 200,
            'lname_min'       => 2,
            'lname_max'       => 200,
            'password_min'    => 6,
            'password_max'    => 32,
            'bulk_insert_max' => 500,
            'bulk_delete_max' => 500,
        ],
    ],
];
