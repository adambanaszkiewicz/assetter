<?php

use Requtize\Assetter\Assetter;
use Requtize\Assetter\PluginInterface;
use Requtize\FreshFile\FreshFile;

class AssetterTest extends PHPUnit_Framework_TestCase
{
    protected function createAssetterObject()
    {
        $ff = new FreshFile(__DIR__.'/.fresh-file');

        $file = $ff->getCacheFilepath();

        if(is_file($file))
            unlink($file);

        return new Assetter($ff);
    }

    public function testChangeDefaultGroup()
    {
        $assetter = $this->createAssetterObject();

        $assetter->setDefaultGroup('new-group');

        $this->assertEquals('new-group', $assetter->getDefaultGroup());
    }
    
    /**
     * @dataProvider providerInitArrayTwo
     */
    public function testGetJs($data)
    {
        $shouldBe = '<script src="http://code.jquery.com/ui/1.11.3/jquery-ui.min.js"></script>';
        $assetter = $this->createAssetterObject();
        $assetter->setCollection($data);

        // Here, should be empty result.
        $this->assertEquals('', $assetter->js());

        $assetter->load('jquery');
        $this->assertEquals($shouldBe, $assetter->js());
    }
    
    /**
     * @dataProvider providerInitArrayTwo
     */
    public function testGetCss($data)
    {
        $shouldBe = '<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.11.3/jquery-ui.min.css" />';
        $assetter = $this->createAssetterObject();
        $assetter->setCollection($data);

        // Here, should be empty result.
        $this->assertEquals('', $assetter->css());

        $assetter->load('jquery');
        $this->assertEquals($shouldBe, $assetter->css());
    }
    
    /**
     * @dataProvider providerInitArrayTwo
     */
    public function testGetAll($data)
    {
        $shouldBe = '<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.11.3/jquery-ui.min.css" />'."\n".'<script src="http://code.jquery.com/ui/1.11.3/jquery-ui.min.js"></script>';
        $assetter = $this->createAssetterObject();
        $assetter->setCollection($data);

        // Here, should be empty result.
        $this->assertEquals("\n", $assetter->all());

        $assetter->load('jquery');
        $this->assertEquals($shouldBe, $assetter->all());
    }
    
    /**
     * @dataProvider providerInitArrayTwo
     */
    public function testDoubleGetAll($data)
    {
        $shouldBe = '<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.11.3/jquery-ui.min.css" />'."\n".'<script src="http://code.jquery.com/ui/1.11.3/jquery-ui.min.js"></script>';
        $assetter = $this->createAssetterObject();
        $assetter->setCollection($data);

        // Here, should be empty result.
        $this->assertEquals("\n", $assetter->all());

        // First...
        $assetter->load('jquery');
        $this->assertEquals($shouldBe, $assetter->all());

        // Second...
        $assetter->load('jquery');
        $this->assertEquals($shouldBe, $assetter->all());
    }

    public static function providerInitArrayTwo()
    {
        return [
            [
                [
                    [
                        'name'  => 'jquery',
                        'files' => [ 'js' => [ 'http://code.jquery.com/ui/1.11.3/jquery-ui.min.js' ], 'css' => [ 'http://code.jquery.com/ui/1.11.3/jquery-ui.min.css' ] ]
                    ]
                ]
            ]
        ];
    }
    
    public function testAlreadyExists()
    {
        $data = [
            [
                'name'  => 'jquery',
                'files' => [ 'js' => [ 'http://code.jquery.com/ui/1.11.3/jquery-ui.min.js' ], 'css' => [ 'http://code.jquery.com/ui/1.11.3/jquery-ui.min.css' ] ]
            ]
        ];

        $assetter = $this->createAssetterObject();
        $assetter->setCollection($data);

        // Here, should be empty result.
        $this->assertEquals(false, $assetter->alreadyLoaded('jquery'));

        // First load call, should load both CSS and JS
        $assetter->load('jquery');
        $this->assertEquals(true, $assetter->alreadyLoaded('jquery'));

        // This one, load not-existed asset, and should not be loaded.
        $assetter->load('not-existed');
        $this->assertEquals(false, $assetter->alreadyLoaded('not-existed'));
    }

    public function testLoadExternalAndAlreadyExists()
    {
        $data = [
            [
                'name'  => 'jquery',
                'files' => [ 'js' => [ 'http://code.jquery.com/ui/1.11.3/jquery-ui.min.js' ], 'css' => [ 'http://code.jquery.com/ui/1.11.3/jquery-ui.min.css' ] ]
            ]
        ];

        $assetter = $this->createAssetterObject();
        $assetter->setCollection($data);

        // Here, every asset should not be loaded
        $this->assertEquals(false, $assetter->alreadyLoaded('jquery'));
        $this->assertEquals(false, $assetter->alreadyLoaded('appended-asset'));

        // Load first asset, and shoud be loaded only one asset.
        $assetter->load('jquery');
        $this->assertEquals(true, $assetter->alreadyLoaded('jquery'));
        $this->assertEquals(false, $assetter->alreadyLoaded('appended-asset'));

        // Load external asset, and shoud be existed.
        $assetter->load([
            'name' => 'appended-asset',
            'files' => []
        ]);
        $this->assertEquals(true, $assetter->alreadyLoaded('jquery'));
        $this->assertEquals(true, $assetter->alreadyLoaded('appended-asset'));
    }

    public function testAppendSimpleAndGet()
    {
        $shouldBe = '<link rel="stylesheet" type="text/css" href="/some/file.css" />'."\n".'<script src="/some/file.js"></script>';
        $assetter = $this->createAssetterObject();

        $this->assertEquals("\n", $assetter->all());

        $assetter->load([
            'name' => 'appended-asset',
            'files' => [
                'js' => [ '/some/file.js' ],
                'css' => [ '/some/file.css' ]
            ]
        ]);

        $assetter->load('appended-asset');
        $this->assertEquals($shouldBe, $assetter->all());
    }

    public function testLoadWithDefinedRevision()
    {
        $data = [
            [
                'name'  => 'jquery',
                'files' => [ 'js' => [ 'http://code.jquery.com/ui/1.11.3/jquery-ui.min.js' => 11 ], 'css' => [ [ 'file' => 'http://code.jquery.com/ui/1.11.3/jquery-ui.min.css', 'revision' => 11 ] ] ]
            ]
        ];

        $shouldBe = '<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.11.3/jquery-ui.min.css?rev=11" />'."\n".'<script src="http://code.jquery.com/ui/1.11.3/jquery-ui.min.js?rev=11"></script>';
        $assetter = $this->createAssetterObject();
        $assetter->setCollection($data);
        $assetter->load('jquery');

        $this->assertEquals($shouldBe, $assetter->all());
    }

    public function testLoadWithGroupsAndGetFromGroup()
    {
        $data = [
            [
                'name'  => 'jquery',
                'files' => [ 'js' => [ 'http://code.jquery.com/ui/1.11.3/jquery-ui.min.js' ], 'css' => [ 'http://code.jquery.com/ui/1.11.3/jquery-ui.min.css' ] ]
            ],
            [
                'name'  => 'one',
                'group' => 'first',
                'files' => [ 'js' => [ 'one/file.js' ], 'css' => [ 'one/file.css' ] ]
            ],
            [
                'name'  => 'two',
                'group' => 'first',
                'files' => [ 'js' => [ 'two/file.js' ], 'css' => [ 'two/file.css' ] ]
            ],
            [
                'name'  => 'three',
                'group' => 'second',
                'files' => [ 'js' => [ 'three/file.js' ], 'css' => [ 'three/file.css' ] ]
            ]
        ];

        $assetter = $this->createAssetterObject();
        $assetter->setDefaultGroup('default-group');
        $assetter->setCollection($data);
        $assetter->load('jquery')->load('one')->load('two')->load('three');

        $this->assertEquals('<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.11.3/jquery-ui.min.css" />'."\n".'<script src="http://code.jquery.com/ui/1.11.3/jquery-ui.min.js"></script>', $assetter->all('default-group'));
        $this->assertEquals('<link rel="stylesheet" type="text/css" href="one/file.css" />'."\n".'<link rel="stylesheet" type="text/css" href="two/file.css" />'."\n".'<script src="one/file.js"></script>'."\n".'<script src="two/file.js"></script>', $assetter->all('first'));
        $this->assertEquals('<link rel="stylesheet" type="text/css" href="three/file.css" />'."\n".'<script src="three/file.js"></script>', $assetter->all('second'));
        $this->assertEquals("\n", $assetter->all('third'));
    }

    /**
     * @dataProvider providerInitArrayEight
     */
    public function testRegisterNamespaces($data)
    {
        $assetter = $this->createAssetterObject();
        $assetter->setCollection($data);
        $assetter->registerNamespace('{NS1}', '/namespace');

        $assetter->load('one')->load('two')->load('three');

        $this->assertEquals('<link rel="stylesheet" type="text/css" href="/file.css" />'."\n".'<script src="/namespace/file.js"></script>', $assetter->all('first'));
        $this->assertEquals('<link rel="stylesheet" type="text/css" href="/namespace/file.css" />'."\n".'<script src="/file.js"></script>', $assetter->all('second'));
        $this->assertEquals('<link rel="stylesheet" type="text/css" href="{NS2}/file.css" />'."\n".'<script src="{NS2}/file.js"></script>', $assetter->all('third'));
    }
    
    /**
     * @dataProvider providerInitArrayEight
     */
    public function testUnregisterNamespaces($data)
    {
        $assetter = $this->createAssetterObject();
        $assetter->setCollection($data);
        $assetter->registerNamespace('{NS1}', '/namespace');

        $assetter->load('one');

        $assetter->unregisterNamespace('{NS1}');

        $assetter->load('two');

        $this->assertEquals('<link rel="stylesheet" type="text/css" href="/file.css" />'."\n".'<script src="/namespace/file.js"></script>', $assetter->all('first'));
        $this->assertEquals('<link rel="stylesheet" type="text/css" href="{NS1}/file.css" />'."\n".'<script src="/file.js"></script>', $assetter->all('second'));
    }

    public static function providerInitArrayEight()
    {
        return [
            [
                [
                    [
                        'name'  => 'one',
                        'group' => 'first',
                        'files' => [ 'js' => [ '{NS1}/file.js' ], 'css' => [ '/file.css' ] ]
                    ],
                    [
                        'name'  => 'two',
                        'group' => 'second',
                        'files' => [ 'js' => [ '/file.js' ], 'css' => [ '{NS1}/file.css' ] ]
                    ],
                    [
                        'name'  => 'three',
                        'group' => 'third',
                        'files' => [ 'js' => [ '{NS2}/file.js' ], 'css' => [ '{NS2}/file.css' ] ]
                    ]
                ]
            ]
        ];
    }
    
    /**
     * @dataProvider providerInitArrayNine
     */
    public function testAssetsOrder($data)
    {
        $assetter = $this->createAssetterObject();
        $assetter->setCollection($data);
        $assetter->load('three')->load('two')->load('four');

        $this->assertEquals('<link rel="stylesheet" type="text/css" href="/one.css" />'
                      ."\n".'<link rel="stylesheet" type="text/css" href="/three.css" />'
                      ."\n".'<link rel="stylesheet" type="text/css" href="/two.css" />'
                      ."\n".'<link rel="stylesheet" type="text/css" href="/four.css" />'
                      ."\n".'<link rel="stylesheet" type="text/css" href="/five.css" />'
                      ."\n", $assetter->all());
    }
    
    /**
     * @dataProvider providerInitArrayNine
     */
    public function testAssetsMultiple($data)
    {
        $assetter = $this->createAssetterObject();
        $assetter->setCollection($data);
        $assetter->load('two')->load('two')->load('five');

        $this->assertEquals('<link rel="stylesheet" type="text/css" href="/one.css" />'
                      ."\n".'<link rel="stylesheet" type="text/css" href="/two.css" />'
                      ."\n".'<link rel="stylesheet" type="text/css" href="/five.css" />'
                      ."\n", $assetter->all());
    }

    /**
     * @dataProvider providerInitArrayNine
     */
    public function testModifyLinksBeforeCompile($data)
    {
        $assetter = $this->createAssetterObject();
        $assetter->registerPlugin(new ModifyLinksBeforeCompilePlugin);

        $assetter->setCollection($data);
        $assetter->load('two')->load('two')->load('five');

        $this->assertEquals('<link rel="stylesheet" type="text/css" href="/one.min.css" />'
                      ."\n".'<link rel="stylesheet" type="text/css" href="/two.min.css" />'
                      ."\n".'<link rel="stylesheet" type="text/css" href="/five.min.css" />'
                      ."\n", $assetter->all());
    }

    public static function providerInitArrayNine()
    {
        return [
            [
                [
                    [
                        'name'  => 'two',
                        'order' => 0,
                        'files' => [ 'css' => [ '/two.css' ] ],
                        'require' => [ 'one', 'five' ]
                    ],
                    [
                        'name'  => 'one',
                        'order' => -10,
                        'files' => [ 'css' => [ '/one.css' ] ]
                    ],
                    [
                        'name'  => 'three',
                        'files' => [ 'css' => [ '/three.css' ] ]
                    ],
                    [
                        'name'  => 'five',
                        'files' => [ 'css' => [ '/five.css' ] ],
                        'order' => 10
                    ],
                    [
                        'order' => 1,
                        'name'  => 'four',
                        'files' => [ 'css' => [ '/four.css' ] ]
                    ]
                ]
            ]
        ];
    }
}

class ModifyLinksBeforeCompilePlugin implements PluginInterface
{
    public function register(Assetter $assetter)
    {
        $assetter->listenEvent('transform-list-to-html', [ $this, 'replaceLinksToMinified' ]);
    }

    public function replaceLinksToMinified(array & $list)
    {
        foreach($list as $key => $files)
        {
            foreach($files['files'] as $fkey => $file)
            {
                $list[$key]['files'][$fkey]['file'] = str_replace('.css', '.min.css', $list[$key]['files'][$fkey]['file']);
            }
        }
    }
} 
