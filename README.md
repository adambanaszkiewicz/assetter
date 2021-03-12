# Assetter
Assets manager for PHP. Allow manage CSS and JS files in website and its dependencies. Also allows refresh cache in browsers by adding revisions of loaded files. Assetter allows you to register namespaces for files paths fo better managing if required.

## Installation

### Via composer.json

```json
{
    "require": {
        "requtize/assetter": "^2.0.0"
    }
}
```

### Via Composer CLI

```cli
composer require requtize/assetter:^2.0.0
```

## Table of contents
* [Basics](#basics)
    1. [Define collection array](#1-define-collection-array)
    2. [Create Assetter object](#2-create-assetter-object)
    3. [Load some libraries](#3-load-some-libraries)
    4. [Show loaded files in document](#4-show-loaded-files-in-document)
* [Namespaces](#namespaces)
    1. [Register namespace](#1-register-namespace)
    2. [Usage registered namespaces](#2-usage-registered-namespaces)
* [LESS ans SCSS Compilers](#less-and-scss-compilers)
    1. [Usage](#1-usage)

## Basics

### 1. Define collection array
First, you have to create array with collection of files.

Here is full indexed array of **one** element in collection.
```php
[
    // Basic asset
    'jquery' => [
        'scripts' => [ 'https://code.jquery.com/jquery-3.6.0.min.js' ],
    ],
    // Full asset
    'theme' => [
        'scripts' => [ '/assets/theme/js/script.min.js' ],
        'styles' => [ '/assets/theme/css/theme.min.css' ],
        'require' => [ 'jquery' ],
        'group' => 'head',
        'order' => 100
    ]
]
```

* **scripts** - Stores array of JavaScript files paths (sigle value *(as string)* **NOT ALLOWED**).
* **styles** - Stores array of CSS files paths (sigle value *(as string)* **NOT ALLOWED**).
* **require** - Stores array of names of libraries/assets (elements from collection) that must be loaded with this library. Dependencies.
* **order** - Number of position in loaded files. Lower number = higher in list.
* **group** - Group this library belongs to.

### 2. Create Assetter object
Now we have to create object of Assetter. To work with Assetter You have to create collection (array) of assets that Assetter can manage.
```php
use Requtize\Assetter\Assetter;
use Requtize\Assetter\Collection;

$assetsCollection = [];
$collection = new Collection($assetsCollection);
$assetter = new Assetter($collection);
```

### 3. Require some libraries
Now, we can require some libraries. Here we require our `theme` asset, which required also `jquery` asset. 
```php
$assetter->require('theme');
```

### 4. Show required files in document
We have required our files. Now it's time to show it in document. To do this, You have to build a collection for group of
the assets, and then get the rendered assets.

```php
// Build default group
$group = $assetter->build();
// Build "body" group
$group = $assetter->build('body');
// Build "head" group
$group = $assetter->build('head');
```

To render the assets tags use one of the following method using the group.

```php
// Returns both CSS and JS files
echo $group->all();
// Returns only CSS files
echo $group->css();
// Returns only JS files
echo $group->js();
```

## Namespaces

You can define namespaces, that will be applied for every asset path, which use it. Think of namespace (in this case) like some Root path to somethig, like root path to images in your project, or to (more for this) assets path. You can register multiple namespaces, and use multiple namespaces in paths to files for assets. What u need.

### 1. Register namespace
To register namespace, call belowed method. First argument is the name of namespace, you want to register. Second argument is a path to some directory. Followed code shows, how register two namespaces.
```php
// Root namespace
$assetter->registerNamespace('{ROOT}', '/web');
// Namespace for global assets
$assetter->registerNamespace('{ASSETS}', '/web/assets');
```
**REMEMBER** - register namespaces before load any asset. Namespaces works only when is needed, so only when you load (or append) any asset. Registered namespaces after load or append won't work.

### 2. Usage registered namespaces
When we have registered namespaces, we only now have to add name of namespace to these files path, we need to. Followed example, shows two assets, one works with namespace, second - without.
```php
[
    // With namespace
    'with' => [
        'js' => [ '{ASSETS}/jquery-ui.min.js' ],
        'css' => [  '{ASSETS}/themes/smoothness/jquery-ui.css' ]
    ],
    // Without namespace
    'without' => [
        'js' => [ 'http://code.jquery.com/ui/1.11.3/jquery-ui.min.js' ],
        'css' => [ 'http://code.jquery.com/ui/1.11.3/themes/smoothness/jquery-ui.css' ]
    ]
]
```
Namespace can be named as you want, here i provide proposal, you can use any way of name convencion, like: {NS}, [NS], %NS, |NS|, -NS, and combine small and big letters as names. But remember to add some special characters. Assetter uses [str_replace](http://php.net/manual/en/function.str-replace.php) for seach and replace namespaces, so if you named namespace with only letters, some assets paths can be damaged.

## How to require CSS in \<head> and JS in \<body>?

This separation of the elements is a common technique, tha allows web browsers renders a fully styled content,
without rendering and downloading any JavaScripts at the beginning. Nowadays we require so many JS libs in our websites,
all those files must be downloaded by browser at the place of occurence. Moving those files at the end of the body
speeds up the page load.

To achive that, You have to define the `head` and the `body` part of the asset, and then mix them together into one,
like this:

```php
[
    // This goes to HEAD, only CSS
    'bootstrap.head' => [
        'styles' => [ 'bootstrap.min.css' ],
        'group' => 'head',
    ],
    // This goes to BODY, all JS
    'bootstrap.body' => [
        'scripts' => [ 'bootstrap.min.js' ],
        'group' => 'body',
    ],
    // This mix it up together
    'bootstrap' => [
        'require' => [ 'bootstrap.head', 'bootstrap.body' ],
    ],
]
```

That defined asset You have to only require using the mixed name `bootstrap`. Assetter take care of the
rest of the assets, and build them into proper defined group in building time.

```php
$assetter->require('bootstrap');
```

Last part, you have to only build assets separate for HEAD and for the BODY. 

```html
<html>
    <head>
        <?php echo $assetter->build('body')->all(); ?>
    </head>
    <body>
        <?php echo $assetter->build('head')->all(); ?>
    </body>
</html>
```
