<?php

return [
    'name'        => 'Messenger',
    'validations' => [
        'extensions'    => [
            'photo'  => ['jpg', 'jpeg', 'png', 'gif',],
            'media'  => ['mp4', 'avi', 'mkv', 'm4a', 'wmv', 'wma', 'mpeg4', 'mp3', 'wbm',],
            'office' => [
                'doc', 'dot', 'wbk', 'docx', 'docm', 'dotx', 'dotm', 'docb', // word
                'xls', 'xlt', 'xlm', 'xlsx', 'xlsm', 'xltx', 'xltm', 'xlsb', 'xla', 'xlam', 'xll', 'xlw', // excel
                'ppt', 'pot', 'pps', 'pptx', 'pptm', 'potx', 'potm', 'ppam', 'ppsx', 'ppsm', 'sldx', 'sldm', // powerpoint
            ],
            'docs'   => ['pdf', 'txt',], //  document
            'files'  => ['zip',],
        ],
        'channel_name'  => 80,
        'topic_name'    => 80,
        'message_reply' => 2048,
        'message_text'  => 2048,
    ],
];
