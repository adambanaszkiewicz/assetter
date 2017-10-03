<?php

error_reporting(-1);

use Requtize\FreshFile\FreshFile;

$start = microtime(true);

$assets = include 'assets.php';

include '../vendor/autoload.php';

// Load Asseter from array of asset conf.
$assetter = new Requtize\Assetter\Assetter(new FreshFile(__DIR__.'/.fresh-file'));

$assetter->registerPlugin(new \Requtize\Assetter\Plugin\LeafoLessPhpPlugin(__DIR__.'/../../../'));
$assetter->registerPlugin(new \Requtize\Assetter\Plugin\LeafoScssPhpPlugin(__DIR__.'/../../../'));

// Register namespaces, if required.
$assetter->registerNamespace('{NAME}', '/some/namespaced/path/to-assets');

// Load simple library
$assetter->load('bootstrap-datetimepicker');

// Load library, user the registered namsepace.
$assetter->load('namespaced-asset');

// Also we can load custom files/assets/libraries by append it.
$assetter->load([
    'files' => [
        'css' => [
            '/github-prod/assetter/demo/assets/less.less',
            '/github-prod/assetter/demo/assets/scss.scss',
            '/github-prod/assetter/demo/assets/bootstrap-scss/bootstrap.scss',
        ]
    ],
    'order' => 101,
    'require' => ['jquery-ui']
])->load([
    'files' => [
        'js' => [
            '/github-prod/assetter/demo/my/own/file.js'
        ],
        'css' => [
            '/github-prod/assetter/demo/my/own/file.css'
        ]
    ],
    'order' => 110,
    'require' => ['jquery-ui']
]);

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
    <?= $assetter->css() ?>
  </head>
  <body>
    <h1><a href="./">Hello, world!</a></h1>
    <div class="less">Works :)</div>
    <div class="scss">Works :)</div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
    <?= $assetter->js() ?>

    <p>Time spend: <?= number_format(microtime(true) - $start, 3) ?></p>
  </body>
</html>
