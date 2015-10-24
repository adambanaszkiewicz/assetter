<?php
/**
 * This file stores simple data of assets that can be used by Assetter.
 * Links to files (even CDNs) are faked!
 */

return [
    /**
     * Followed jQuery UI element have all indexes used by Assetter.
     */
    [
        // Name of library. Required.
        'name' => 'jquery',
        // Ordering. Lower number = higher in loading list. Default order = 100
        'order' => 0,
        // Group belogs to.
        'group' => 'top',
        // Revision of this files. Overwrite default revision.
        'revision' => 7,
        // List of files. Required.
        'files' => [
            // JavaScript files
            'js' => [
                'http://code.jquery.com/ui/1.11.3/jquery-ui.min.js'
            ],
            // CSS files
            'css' => [
                'http://code.jquery.com/ui/1.11.3/themes/smoothness/jquery-ui.css'
            ]
        ],
        // List of names of libraries, that must be required with this one.
        'require' => [
            'jquery'
        ]
    ],

    [
        'name' => 'jquery',
        'files' => [
            'js' => [
                'http://cdn.jquery.com/jquery.min.js'
            ]
        ]
    ],
    [
        'name' => 'momentjs',
        'order' => 10,
        'files' => [
            'js' => [
                '/js/moment/moment-2.10.3.min.js'
            ]
        ],
        'require' => [
            'jquery'
        ]
    ],
    [
        'name' => 'bootstrap-datetimepicker',
        'files' => [
            'css' => [
                '/js/bt-dtp/style.css',
                '/js/bt-dtp/theme-def.css',
            ],
            'js' => [
                '/js/bt-dtp/bootstrap-datetimepicker.min.js'
            ]
        ],
        'require' => [
            'jquery',
            'momentjs'
        ]
    ],
    [
        'name' => 'namespaced-asset',
        'files' => [
            'css' => [
                '{NAME}/ns-asset/style.css',
            ],
            'js' => [
                '{NAME}/ns-asset/bootstrap-datetimepicker.min.js'
            ]
        ],
        'require' => [
            'jquery',
            'momentjs'
        ]
    ]
];
