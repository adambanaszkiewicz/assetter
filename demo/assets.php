<?php
/**
 * This file stores simple data of assets that can be used by Assetter.
 * Links to files (even CDNs) are faked!
 */

return [
    'bootstrap' => [
        'require' => [ 'bootstrap.body', 'bootstrap.head' ],
    ],
    'bootstrap.body' => [
        'scripts' => ['https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js'],
        'require' => [ 'popperjs', 'jquery' ],
    ],
    'bootstrap.head' => [
        'styles' => ['https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css'],
        'group' => 'head',
    ],
    'jquery' => [
        'scripts' => ['https://code.jquery.com/jquery-3.2.1.slim.min.js'],
        'group' => 'head',
    ],
    'popperjs' => [
        'scripts' => ['https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js'],
        'group' => 'head',
    ],
    'momentjs' => [
        'scripts' => ['/js/moment/moment-2.10.3.min.js'],
        'require' => ['jquery'],
    ],
    'bootstrap-datetimepicker' => [
        'styles' => [
            '/js/bt-dtp/style.css',
            '/js/bt-dtp/theme-def.css',
        ],
        'scripts' => [
            '/js/bt-dtp/bootstrap-datetimepicker.min.js'
        ],
        'require' => [
            'jquery',
            'momentjs'
        ]
    ],
    'namespaced-asset' => [
        'styles' => [
            '{NAME}/ns-asset/style.css',
        ],
        'scripts' => [
            '{NAME}/ns-asset/bootstrap-datetimepicker.min.js'
        ],
        'require' => [
            'jquery',
            'momentjs'
        ]
    ],
    'existing-asset' => [
        'styles' => [ 'assets/style.css' ],
        'scripts' => [ 'assets/script.js' ],
        'require' => [ 'bootstrap' ]
    ]
];
