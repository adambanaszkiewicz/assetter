<?php

use Requtize\Assetter\Assetter;
use Requtize\Assetter\Collection;

error_reporting(-1);

$start = microtime(true);

$assets = include 'assets.php';

include '../vendor/autoload.php';

// Load Asseter from array of asset conf.
$assetter = new Assetter(new Collection($assets, 'body'));

// Register namespaces, if required.
$assetter->registerNamespace('{NAME}', '/some/namespaced/path/to-assets');

// Load simple library
$assetter->require('bootstrap-datetimepicker');

// Load library, user the registered namsepace.
$assetter->require('namespaced-asset');

// Load existing asset
$assetter->require('existing-asset');

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <?= $assetter->build('head')->all(); ?>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h1><a href="./">Hello, world!</a></h1>
                    <p id="asset-info"></p>

                    <?= $assetter->build('body')->all(); ?>
                    <p>Time spend: <?= number_format(microtime(true) - $start, 5) ?>s.</p>
                </div>
            </div>
        </div>
    </body>
</html>
