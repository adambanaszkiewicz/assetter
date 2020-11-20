<?php

declare(strict_types=1);

/**
 * @license   MIT License
 * @copyright Copyright (c) 2016 - 2020, Adam Banaszkiewicz
 * @link      https://github.com/requtize/assetter
 */
namespace Requtize\Assetter\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Requtize\Assetter\Assetter;
use Requtize\Assetter\AssetterInterface;
use Requtize\Assetter\Collection;
use Requtize\Assetter\Exception\MissingAssetException;
use Requtize\Assetter\PluginInterface;

/**
 * @author Adam Banaszkiewicz https://github.com/requtize
 */
class RendererTest extends TestCase
{
    /**
     * @dataProvider providerComplexCollectionDependencies
     * @throws MissingAssetException
     */
    /*public function testEmpty($data): void
    {
        $assetter = new Assetter(new Collection($data));

        $bundle = $assetter->build();

        $this->assertEquals('', $bundle->scripts());
        $this->assertEquals('', $bundle->styles());
        $this->assertEquals('', $bundle->all());
    }*/

    /**
     * @dataProvider providerComplexCollectionDependencies
     * @throws MissingAssetException
     */
    /*public function testGetScripts($data): void
    {
        $assetter = new Assetter(new Collection($data));

        $shouldBe = '<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>'
            ."\n".'<script src="http://code.jquery.com/ui/1.11.3/jquery-ui.min.js"></script>';

        $assetter->require('jquery-ui');
        $bundle = $assetter->build();

        $this->assertEquals($shouldBe, $bundle->scripts());
    }*/

    /**
     * @dataProvider providerComplexCollectionDependencies
     * @throws MissingAssetException
     */
    /*public function testGetStyles($data): void
    {
        $assetter = new Assetter(new Collection($data));

        $assetter->require('jquery-ui');
        $bundle = $assetter->build();

        $shouldBe = '<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.11.3/jquery-ui.min.css" />';

        $this->assertEquals($shouldBe, $bundle->styles());
    }*/

    /**
     * @dataProvider providerComplexCollectionDependencies
     * @throws MissingAssetException
     */
    /*public function testGetAll($data): void
    {
        $assetter = new Assetter(new Collection($data));

        $assetter->require('jquery-ui');
        $bundle = $assetter->build();

        $shouldBe = '<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.11.3/jquery-ui.min.css" />'
            ."\n".'<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>'
            ."\n".'<script src="http://code.jquery.com/ui/1.11.3/jquery-ui.min.js"></script>';

        $this->assertEquals($shouldBe, $bundle->all());
    }*/

    /**
     * @return array
     */
    /*public static function providerComplexCollectionDependencies(): array
    {
        return [
            [
                [
                    'jquery' => [
                        'scripts' => [ 'https://code.jquery.com/jquery-3.4.1.min.js' ],
                    ],
                    'popper' => [
                        'scripts' => [ 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/popper.min.js' ],
                    ],
                    'jquery-ui' => [
                        'scripts' => [ 'http://code.jquery.com/ui/1.11.3/jquery-ui.min.js' ],
                        'styles'  => [ 'http://code.jquery.com/ui/1.11.3/jquery-ui.min.css' ],
                        'require' => [ 'jquery' ],
                    ],
                    'bootstrap' => [
                        'scripts' => [ 'https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js' ],
                        'styles'  => [ 'https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css' ],
                        'require' => [ 'jquery', 'popper' ],
                    ],
                    'custom-assets' => [
                        'scripts' => [ 'https://domain.com/assets/script.js' ],
                        'styles'  => [ 'https://domain.com/assets/style.css' ],
                        'require' => [ 'bootstrap' ],
                    ],
                    'missing-dependency-root' => [
                        'require' => [ 'missing-dependency' ],
                    ],
                ],
            ],
        ];
    }*/

    /**
     * @throws MissingAssetException
     */
    /*public function testRequireRevision(): void
    {
        $data = [
            'jquery-ui' => [
                'scripts' => [ 'http://code.jquery.com/ui/1.11.3/jquery-ui.min.js', 'http://code.jquery.com/ui/1.11.3/jquery-ui.min.js?query_string=1' ],
                'styles' => [ 'http://code.jquery.com/ui/1.11.3/jquery-ui.min.css', 'http://code.jquery.com/ui/1.11.3/jquery-ui.min.css?query_string=1' ],
                'revision' => 11,
            ],
        ];

        $assetter = new Assetter(new Collection($data));

        $assetter->require('jquery-ui');
        $bundle = $assetter->build();

        $shouldBe = '<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.11.3/jquery-ui.min.css?rev=11" />'
            ."\n".'<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.11.3/jquery-ui.min.css?query_string=1&rev=11" />'
            ."\n".'<script src="http://code.jquery.com/ui/1.11.3/jquery-ui.min.js?rev=11"></script>'
            ."\n".'<script src="http://code.jquery.com/ui/1.11.3/jquery-ui.min.js?query_string=1&rev=11"></script>';

        $this->assertEquals($shouldBe, $bundle->all());
    }*/

    /**
     * @throws MissingAssetException
     */
    /*public function testGroups(): void
    {
        $data = [
            'jquery' => [
                'scripts' => [ 'http://code.jquery.com/ui/1.11.3/jquery-ui.min.js' ],
                'styles' => [ 'http://code.jquery.com/ui/1.11.3/jquery-ui.min.css' ],
            ],
            'one' => [
                'group' => 'first',
                'scripts' => [ 'one/file.js' ],
                'styles' => [ 'one/file.css' ],
            ],
            'two' => [
                'group' => 'first',
                'scripts' => [ 'two/file.js' ],
                'styles' => [ 'two/file.css' ],
            ],
            'three' => [
                'group' => 'second',
                'scripts' => [ 'three/file.js' ],
                'styles' => [ 'three/file.css' ],
            ]
        ];

        $assetter = new Assetter(new Collection($data));

        $assetter->require('jquery', 'one', 'two', 'three');
        $bundle = $assetter->build();

        $this->assertEquals(
            '<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.11.3/jquery-ui.min.css" />'
            ."\n".'<script src="http://code.jquery.com/ui/1.11.3/jquery-ui.min.js"></script>',
            $bundle->all('def')
        );

        $this->assertEquals(
            '<link rel="stylesheet" type="text/css" href="one/file.css" />'
            ."\n".'<link rel="stylesheet" type="text/css" href="two/file.css" />'
            ."\n".'<script src="one/file.js"></script>'
            ."\n".'<script src="two/file.js"></script>',
            $bundle->all('first')
        );

        $this->assertEquals(
            '<link rel="stylesheet" type="text/css" href="three/file.css" />'
            ."\n".'<script src="three/file.js"></script>',
            $bundle->all('second')
        );

        $this->assertEquals('', $bundle->all('third'));
    }*/

    /**
     * @dataProvider providerInitArrayEight
     *
     * @param array $data
     *
     * @throws MissingAssetException
     */
    /*public function testRegisterNamespaces(array $data): void
    {
        $assetter = new Assetter(new Collection($data));
        $assetter->registerNamespace('{NS1}', '/namespace');

        $assetter->require('one');
        $assetter->require('two');
        $assetter->require('three');

        $bundle = $assetter->build();

        $this->assertEquals('<link rel="stylesheet" type="text/css" href="/file.css" />'
            . "\n" . '<script src="/namespace/file.js"></script>',
            $bundle->all('first'));

        $this->assertEquals('<link rel="stylesheet" type="text/css" href="/namespace/file.css" />'
            ."\n".'<script src="/file.js"></script>',
            $bundle->all('second'));

        $this->assertEquals('<link rel="stylesheet" type="text/css" href="{NS2}/file.css" />'
            ."\n".'<script src="{NS2}/file.js"></script>',
            $bundle->all('third'));
    }*/

    /**
     * @dataProvider providerInitArrayEight
     *
     * @param array $data
     *
     * @throws MissingAssetException
     */
    /*public function testUnregisterNamespaces(array $data): void
    {
        $assetter = new Assetter(new Collection($data));
        $assetter->registerNamespace('{NS1}', '/namespace');
        $assetter->require('one');
        $bundle = $assetter->build();

        $this->assertEquals('<link rel="stylesheet" type="text/css" href="/file.css" />'
            . "\n" . '<script src="/namespace/file.js"></script>',
            $bundle->all('first'));

        $assetter->unregisterNamespace('{NS1}');
        $assetter->require('two');
        $bundle = $assetter->build();

        $this->assertEquals('<link rel="stylesheet" type="text/css" href="{NS1}/file.css" />'
            . "\n" . '<script src="/file.js"></script>',
            $bundle->all('second'));
    }*/

    /**
     * @return array
     */
    /*public static function providerInitArrayEight(): array
    {
        return [
            [
                [
                    'one' => [
                        'group' => 'first',
                        'scripts' => [ '{NS1}/file.js' ],
                        'styles' => [ '/file.css' ],
                    ],
                    'two' => [
                        'group' => 'second',
                        'scripts' => [ '/file.js' ],
                        'styles' => [ '{NS1}/file.css' ],
                    ],
                    'three' => [
                        'group' => 'third',
                        'scripts' => [ '{NS2}/file.js' ],
                        'styles' => [ '{NS2}/file.css' ],
                    ],
                ],
            ],
        ];
    }*/

    /**
     * @dataProvider providerInitArrayNine
     *
     * @param array $data
     *
     * @throws MissingAssetException
     */
    /*public function testAssetsOrder(array $data): void
    {
        $assetter = new Assetter(new Collection($data));
        $assetter->require('two', 'three', 'four');
        $bundle = $assetter->build();

        $this->assertEquals('<link rel="stylesheet" type="text/css" href="/five.css" />'
            . "\n" . '<link rel="stylesheet" type="text/css" href="/four.css" />'
            . "\n" . '<link rel="stylesheet" type="text/css" href="/two.css" />'
            . "\n" . '<link rel="stylesheet" type="text/css" href="/three.css" />'
            . "\n" . '<link rel="stylesheet" type="text/css" href="/one.css" />',
            $bundle->all());
    }*/

    /**
     * @dataProvider providerInitArrayNine
     */
    /*private function testModifyLinksBeforeCompile($data): void
    {
        $assetter = $this->createAssetterObject();
        $assetter->registerPlugin(new ModifyLinksBeforeCompilePlugin);

        $assetter->setCollection($data);
        $assetter->load('two');
        $assetter->load('two');
        $assetter->load('five');

        $this->assertEquals('<link rel="stylesheet" type="text/css" href="/one.min.css" />'
                      ."\n".'<link rel="stylesheet" type="text/css" href="/two.min.css" />'
                      ."\n".'<link rel="stylesheet" type="text/css" href="/five.min.css" />'
                      ."\n", $assetter->all());
    }*/

    /**
     * @return array
     */
    /*public static function providerInitArrayNine(): array
    {
        return [
            [
                [
                    'one' => [
                        'priority' => -10,
                        'styles' => [ '/one.css' ],
                    ],
                    'five' => [
                        'styles' => [ '/five.css' ],
                        'priority' => 10,
                    ],
                    'two' => [
                        'styles' => [ '/two.css' ],
                    ],
                    'four' => [
                        'priority' => 1,
                        'styles' => [ '/four.css' ],
                    ],
                    'three' => [
                        'priority' => 0,
                        'styles' => [ '/three.css' ],
                        'require' => [ 'one', 'five' ],
                    ],
                ]
            ]
        ];
    }*/
}


/**
 * @author Adam Banaszkiewicz https://github.com/requtize
 */
class ModifyLinksBeforeCompilePlugin implements PluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(AssetterInterface $assetter): void
    {
        $assetter->listenEvent('transform-list-to-html', [ $this, 'replaceLinksToMinified' ]);
    }

    /**
     * @param array $list
     */
    public function replaceLinksToMinified(array & $list): void
    {
        foreach ($list as $key => $files) {
            foreach ($files['files'] as $fkey => $file) {
                $list[$key]['files'][$fkey]['file'] = str_replace('.css', '.min.css', $list[$key]['files'][$fkey]['file']);
            }
        }
    }
}
