# Assetter
Assets manager for PHP. Allow manage CSS and JS files in website and its dependencies. Also allows refresh cache in browsers by adding revisions of loaded files. Assetter allows you to register namespaces for files paths fo better managing if required.

Assetter now allows You to load Your SASS and LESS styles directly to Your project, and compile them just-in-time and load compiled styles in the browser. You have to only register any of built-in plugins to enable this functionality (and install suggested composer packages).

## Installation

### Via composer.json

```json
{
    "require": {
        "requtize/assetter": "^1.0.0"
    }
}
```

### Via Composer CLI

```cli
composer require requtize/assetter:^1.0.0
```

## Table of contents
* [Basics](#basics)
    1. [Define collection array](#1-define-collection-array)
    2. [Create Assetter object](#2-create-assetter-object)
    3. [Load some libraries](#3-load-some-libraries)
        * [Load custom library](#3a-load-custom-library)
    4. [Show loaded files in document](#4-show-loaded-files-in-document)
* [Namespaces](#namespaces)
    1. [Register namespace](#1-register-namespace)
    2. [Usage registered namespaces](#2-usage-registered-namespaces)
* [LESS ans SCSS Compilers](#less-and-scss-compilers)
    1. [Usage](#1-usage)

## Basics

### 1. Define collection array
First, you have to create array with collection of files.

Here is full indexed array of **one** element in collection. **Collection is an array of arrays (check out**  *demo* **provided with this file).**
```php
[
    'name'  => 'jquery',
    'order' => 0,
    'group' => 'head',
    'files' => [
        'js' => [/* ... */],
        'css' => [/* ... */]
    ],
    'require' => [
        'jquery'
    ]
]
```
* **name** *(Required*) - Name of library/assets files list.
* **order** *(Optional*) - Number of position in loaded files. Lower number = higher in list.
* **group** *(Optional*) - Group this library belongs to.
* **files** *(Required*) - Store JS and CSS files' array to load.
    * **js** *(Required*) - Stores array of JavaScript files paths (sigle value *(as string)* **NOT ALLOWED**).
    * **css** *(Required*) - Stores array of CSS files paths (sigle value *(as string)* **NOT ALLOWED**).
* **require** *(Optional*) - Stores array of names of libraries/assets (elements from collection) that must be loaded with this library. Dependencies.

### 2. Create Assetter object
Now we have to create object of Assetter. Assetter takes as it's argument object to FreshFile (fresh-file package is installed automatically with Assetter).
```php
use Requtize\FreshFile\FreshFile;
use Requtize\Assetter\Assetter;

$ff = new FreshFile($pathToCacheFile);
$assetter = new Assetter($ff);
```
### 2a. Set assets collection
To work with Assetter You have to create collection (array) of assets that Assetter can manage.
```php
$assetter->setCollection($collection);
```

### 3. Load some libraries
Now, we can load some libraries
```php
$assetter->load('bootstrap-datetimepicker');
```

### 3a. Load custom library
We can also include custom libraries, that aren't in our defined collection. We have to call *load()* method, and pass array with exactly the same indexes like in our collection we define earlier. We have to define only one index - files, rest of indexes are optional in this case.
```php
$assetter->load([
    'files' => [
        'js' => [
            '/my/own/file.js'
        ]
    ]
]);
```

### 4. Show loaded files in document
We have loaded our files. Now it's time to show it in document. For this, you can use three methods from Assetter that allows you to do this:
* **all()** - Returns both CSS and JS files.
* **css()** - Returns only CSS files.
* **js()** - Returns only JS files.

Every function accept one argument, with group name to returns. If you pass "*" or leave empty, Assetter returns files (of selected type) from all groups.

```php
// In HEAD of document we show all CSS (from all groups) and JS only from 'head' group.
echo $assetter->css();
echo $assetter->js('head');

// After </body> tag we shows rest of JS
echo $assetter->js('body');

// Or just show from all groups
echo $assetter->all();
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
// With namespace
[
    'name'  => 'with',
    'files' => [
        'js' => [
            '{ASSETS}/jquery-ui.min.js'
        ],
        'css' => [
            '{ASSETS}/themes/smoothness/jquery-ui.css'
        ]
    ]
],
// Without namespace
[
    'name'  => 'without',
    'files' => [
        'js' => [
            'http://code.jquery.com/ui/1.11.3/jquery-ui.min.js'
        ],
        'css' => [
            'http://code.jquery.com/ui/1.11.3/themes/smoothness/jquery-ui.css'
        ]
    ]
]
```
Namespace can be named as you want, here i provide proposal, you can use any way of name convencion, like: {NS}, [NS], %NS, |NS|, -NS, and combine small and big letters as names. But remember to add some special characters. Assetter uses [str_replace](http://php.net/manual/en/function.str-replace.php) for seach and replace namespaces, so if you named namespace with only letters, some assets paths can be damaged.


# LESS ans SCSS Compilers

Assetter have built-in plugins for these compilers. To turn on this functionality, create selected plugin object, and pass it to register it:
```php
use Requtize\Assetter\Plugin\LeafoLessPhpPlugin;
use Requtize\Assetter\Plugin\LeafoScssPhpPlugin;

$assetter->registerPlugin(new LeafoLessPhpPlugin($basepath));
$assetter->registerPlugin(new LeafoScssPhpPlugin($basepath));
```
Both plugins takes the argument of base path (from server root) to place, where application is placed - other words: the path to domain directory. You can albo create only one plugin, if You want use only one pre-processor.

### Usage

To use, you have to only load the .scss or .less files from collection:

```php
[
    'name'  => 'less-and-scss',
    'files' => [
        'css' => [
            '/path/to/style.scss',
            '/path/to/style.less',
        ]
    ]
]
```

Plugins automatically compile these files, it they are changed, and replace filenames to compiled versions. Also, FreshFile lib takes care of compilation cache, so once compiled file will not compile again until you change it.

# @todo
- DOC: Multiple versions of files array
- DOC: Revisions
- Add filemtime revisions instead of static numbers.
