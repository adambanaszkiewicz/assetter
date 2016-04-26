<?php

$assets = include 'assets.php';

include '../src/Assetter/Assetter.php';

// Load Asseter from array of asset conf.
$assetter = new Requtize\Assetter\Assetter($assets);

// Register namespaces, if required.
$assetter->registerNamespace('{NAME}', '/some/namespaced/path/to-assets');

// Load simple library
$assetter->load('bootstrap-datetimepicker');

// Load library, user the registered namsepace.
$assetter->load('namespaced-asset');

// Also we can load custom files/assets/libraries by append it.
$assetter->load([
    'files' => [
        'js' => [
            '/my/own/file.js'
        ],
        'css' => [
            '/my/own/file.css'
        ]
    ],
    'order' => 101,
    'require' => ['jquery-ui']
]);

echo '<pre>'.($assetter->css())."\n".($assetter->js()).'</pre>';
