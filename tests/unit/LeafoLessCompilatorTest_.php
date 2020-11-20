<?php

/**
 * @license   MIT License
 * @copyright Copyright (c) 2016 - 2020, Adam Banaszkiewicz
 * @link      https://github.com/requtize/assetter
 */
namespace Requtize\Assetter\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Requtize\Assetter\Assetter;
use Requtize\FreshFile\FreshFile;

class LeafoLessCompilatorTest extends TestCase
{
    protected function createAssetterObject()
    {
        $ff = new FreshFile(__DIR__.'/resources/.fresh-file');

        $file = $ff->getCacheFilepath();

        if(is_file($file))
            unlink($file);

        $assetter = new Assetter($ff);
        $assetter->registerPlugin(new \Requtize\Assetter\Plugin\LeafoLessPhpPlugin(__DIR__.'/resources'));

        return $assetter;
    }

    protected function setCollectionAndLoadIt(Assetter $assetter)
    {
        $assetter->setCollection([
            [
                'name'  => 'css',
                'files' => [ 'css' => [ '/css.css' ] ],
            ],
            [
                'name'  => 'scss',
                'files' => [ 'css' => [ '/scss.scss' ] ]
            ],
            [
                'name'  => 'less',
                'files' => [ 'css' => [ '/less.less' ] ]
            ]
        ]);

        $assetter->load('css')->load('scss')->load('less');
    }

    protected function removeFiles()
    {
        $files = [
            __DIR__.'/resources/less.css',
            __DIR__.'/.fresh-file'
        ];

        foreach($files as $file)
        {
            clearstatcache(true, $file);

            if(is_file($file))
                unlink($file);
        }
    }

    public function __testCheckFileExtensionChange()
    {
        if(class_exists('lessc') === false)
            return;

        $basepath = __DIR__.'/resources';
        $this->removeFiles();

        $assetter = $this->createAssetterObject();
        $this->setCollectionAndLoadIt($assetter);

        $fmt = filemtime($basepath.'/less.less');

        // Detect if Plugin replaces the filepath of LESS file,
        // and leave rest of paths untouched.
        $this->assertEquals('<link rel="stylesheet" type="text/css" href="/css.css" />'
                      ."\n".'<link rel="stylesheet" type="text/css" href="/less.css?rev='.$fmt.'" />'
                      ."\n".'<link rel="stylesheet" type="text/css" href="/scss.scss" />'
                      ."\n", $assetter->all());

        $this->removeFiles();
    }

    public function __testCompiledFileExists()
    {
        if(class_exists('lessc') === false)
            return;

        $basepath = __DIR__.'/resources';
        $this->removeFiles();

        $this->assertFalse(is_file($basepath.'/less.css'));

        $assetter = $this->createAssetterObject();
        $this->setCollectionAndLoadIt($assetter);
        $assetter->all();

        $this->assertTrue(is_file($basepath.'/less.css'));

        $this->removeFiles();
    }

    public function __testCompiledFileContent()
    {
        if(class_exists('lessc') === false)
            return;

        $basepath = __DIR__.'/resources';
        $this->removeFiles();

        $assetter = $this->createAssetterObject();
        $this->setCollectionAndLoadIt($assetter);
        $assetter->all();

        $this->assertEquals(file_get_contents($basepath.'/less.css.test'), file_get_contents($basepath.'/less.css'));

        $this->removeFiles();
    }

    public function __testCompiledFileCache()
    {
        if(class_exists('lessc') === false)
            return;

        $basepath = __DIR__.'/resources';
        $this->removeFiles();

        $assetter = $this->createAssetterObject();
        $this->setCollectionAndLoadIt($assetter);
        $assetter->all();

        $plugin = $assetter->getRegisteredPlugins()[0];

        $lessFile = $basepath.'/less.less';
        $cssFile  = $basepath.'/less.css';

        $this->assertTrue(is_file($lessFile));
        $this->assertTrue(is_file($cssFile));

        unlink($cssFile);
        $this->assertFalse(is_file($cssFile));

        // Compile again should not create again this file.
        $plugin->compile($lessFile);
        $this->assertFalse(is_file($cssFile));

        // Touching should create file again.
        touch($lessFile, time() + 10);
        $plugin->compile('/less.less');

        $this->assertTrue(is_file($cssFile));

        unlink($cssFile);
        $plugin->compile('/less.less');
        $this->assertFalse(is_file($cssFile));

        $this->removeFiles();
    }
}
