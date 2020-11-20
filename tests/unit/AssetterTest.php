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
use Requtize\Assetter\Collection;
use Requtize\Assetter\Exception\MissingAssetException;

/**
 * @author Adam Banaszkiewicz https://github.com/requtize
 */
class AssetterTest extends TestCase
{
    /**
     * @dataProvider providerComplexCollectionDependencies
     *
     * @param array $data
     *
     * @throws MissingAssetException
     */
    public function testRequire(array $data): void
    {
        $collection = new Collection($data);
        $assetter = new Assetter($collection);

        // Here, should be empty result.
        $this->assertEquals([], $assetter->getRequired());

        $assetter->require('jquery', 'custom-assets');
        $this->assertEquals(['jquery', 'custom-assets'], $assetter->getRequired());
    }

    /**
     * @dataProvider providerComplexCollectionDependencies
     *
     * @param array $data
     *
     * @throws MissingAssetException
     */
    public function testRequireMissingRoot(array $data): void
    {
        $this->expectException(MissingAssetException::class);

        $collection = new Collection($data);
        $assetter = new Assetter($collection);

        $assetter->require('missing-asset');
    }

    /**
     * @dataProvider providerComplexCollectionDependencies
     *
     * @param array $data
     *
     * @throws MissingAssetException
     */
    public function testUnrequire(array $data): void
    {
        $collection = new Collection($data);
        $assetter = new Assetter($collection);

        // Here, should be empty result.
        $this->assertEquals([], $assetter->getRequired());

        $assetter->require('jquery', 'custom-assets');
        $this->assertEquals(['jquery', 'custom-assets'], $assetter->getRequired());

        $assetter->unrequire('jquery');

        $this->assertEquals(['custom-assets'], $assetter->getRequired());
    }

    /**
     * @dataProvider providerComplexCollectionDependencies
     *
     * @param array $data
     *
     * @throws MissingAssetException
     */
    public function testBundle(array $data): void
    {
        $collection = new Collection($data);
        $assetter = new Assetter($collection);

        // Here, should be empty result.
        $this->assertEquals([], $assetter->getRequired());

        $assetter->require('custom-assets');
        $renderer = $assetter->build();

        $this->assertEquals(
            ['jquery', 'popper', 'bootstrap', 'custom-assets'],
            array_keys($renderer->getPayload())
        );
    }

    /**
     * @dataProvider providerComplexCollectionDependencies
     *
     * @param array $data
     *
     * @throws MissingAssetException
     */
    public function testBundleMissingDependency(array $data): void
    {
        $this->expectException(MissingAssetException::class);

        $collection = new Collection($data);
        $assetter = new Assetter($collection);

        $assetter->require('missing-dependency-root');
        $assetter->build();
    }

    /**
     * @return array
     */
    public static function providerComplexCollectionDependencies(): array
    {
        return [
            [
                [
                    'jquery' => [
                        'scripts' => [ 'https://code.jquery.com/jquery-3.4.1.min.js' ],
                        'group' => 'head',
                    ],
                    'popper' => [
                        'scripts' => [ 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/popper.min.js' ],
                    ],
                    'jquery-ui' => [
                        'scripts' => [ 'http://code.jquery.com/ui/1.11.3/jquery-ui.min.js' ],
                        'styles'  => [ 'http://code.jquery.com/ui/1.11.3/jquery-ui.min.css' ],
                        'group' => 'head',
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
    }
}
