<?php

use Leafo\ScssPhp\Compiler;
use Leafo\ScssPhp\Formatter\Crunched;
use Requtize\Assetter\Assetter;
use Requtize\FreshFile\FreshFile;

class LeafoScssCompilatorTest extends PHPUnit_Framework_TestCase
{
    protected function createAssetterObject()
    {
        $ff = new FreshFile(__DIR__.'/resources/.fresh-file');

        $file = $ff->getCacheFilepath();

        if(is_file($file))
            unlink($file);

        $assetter = new Assetter($ff);
        $assetter->registerPlugin(new \Requtize\Assetter\Plugin\LeafoScssPhpPlugin(__DIR__.'/resources'));

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
            __DIR__.'/resources/scss.css',
            __DIR__.'/.fresh-file'
        ];

        foreach($files as $file)
        {
            clearstatcache(true, $file);

            if(is_file($file))
                unlink($file);
        }
    }

    public function testCheckFileExtensionChange()
    {
        if(class_exists(Compiler::class) === false)
            return;

        $basepath = __DIR__.'/resources';
        $this->removeFiles();

        $assetter = $this->createAssetterObject();
        $this->setCollectionAndLoadIt($assetter);

        $fmt = filemtime($basepath.'/scss.scss');

        // Detect if Plugin replaces the filepath of SCSS file,
        // and leave rest of paths untouched.
        $this->assertEquals('<link rel="stylesheet" type="text/css" href="/css.css" />'
                      ."\n".'<link rel="stylesheet" type="text/css" href="/less.less" />'
                      ."\n".'<link rel="stylesheet" type="text/css" href="/scss.css?rev='.$fmt.'" />'
                      ."\n", $assetter->all());

        $this->removeFiles();
    }

    public function testCompiledFileExists()
    {
        if(class_exists(Compiler::class) === false)
            return;

        $basepath = __DIR__.'/resources';
        $this->removeFiles();

        $this->assertFalse(is_file($basepath.'/scss.css'));

        $assetter = $this->createAssetterObject();
        $this->setCollectionAndLoadIt($assetter);
        $assetter->all();

        $this->assertTrue(is_file($basepath.'/scss.css'));

        $this->removeFiles();
    }

    public function testCompiledFileContent()
    {
        if(class_exists(Compiler::class) === false)
            return;

        $basepath = __DIR__.'/resources';
        $this->removeFiles();

        $assetter = $this->createAssetterObject();
        $this->setCollectionAndLoadIt($assetter);
        $assetter->all();

        $this->assertEquals(file_get_contents($basepath.'/scss.css.test'), file_get_contents($basepath.'/scss.css'));

        $this->removeFiles();
    }

    public function testCompiledFileCache()
    {
        if(class_exists(Compiler::class) === false)
            return;

        $basepath = __DIR__.'/resources';
        $this->removeFiles();

        $assetter = $this->createAssetterObject();
        $this->setCollectionAndLoadIt($assetter);
        $assetter->all();

        $plugin = $assetter->getRegisteredPlugins()[0];

        $lessFile = $basepath.'/scss.scss';
        $cssFile  = $basepath.'/scss.css';

        $this->assertTrue(is_file($lessFile));
        $this->assertTrue(is_file($cssFile));

        unlink($cssFile);
        $this->assertFalse(is_file($cssFile));

        // Compile again should not create again this file.
        $plugin->compile($lessFile);
        $this->assertFalse(is_file($cssFile));

        // Touching should create file again.
        touch($lessFile, time() + 10);
        $plugin->compile('/scss.scss');

        $this->assertTrue(is_file($cssFile));

        unlink($cssFile);
        $plugin->compile('/scss.scss');
        $this->assertFalse(is_file($cssFile));

        $this->removeFiles();
    }
}
