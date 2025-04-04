<?php

require_once ZS_FEEDBACK_DIR . 'vendor/autoload.php';
const ZS_FEEDBACK_VERSION = '0.1.0';
const OPTIONS = [
    '0.1.0' => [
        'OPTS'  => [
            'ADD'       => [ ],
            'DEL'       => [ ],
            'UPD'       => [ ],
        ],
        'DB'    => [
            'TABLE' => [
                'NEW'   => [ ],
                'DEL'   => [ ],
                'UPD'   => [ ],
            ],
            'DATA' => [
                'INS'   => [ ],
                'UPD'   => [ ],
                'DEL'   => [ ],
            ],
        ],
    ]
];

$zs_feedback_admin = new ZS\FeedbackAdmin( );
$zs_feedback = new ZS\Feedback( );