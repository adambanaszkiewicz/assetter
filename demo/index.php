<?php

$assets = include 'assets.php';

include '../src/Assetter/Assetter.php';
include '../src/Assetter/PluginInterface.php';
include '../src/Assetter/Plugin/LeafoLessPhpPlugin.php';

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
            '/my/own/file.css',
            '/assets/less.less',
            '/assets/scss.scss'
        ]
    ],
    'order' => 101,
    'require' => ['jquery-ui']
]);

$assetter->registerPlugin(new \Requtize\Assetter\Plugin\LeafoLessPhpPlugin);

echo '<pre>'.($assetter->css())."\n".($assetter->js()).'</pre>';
