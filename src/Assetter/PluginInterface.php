<?php

declare(strict_types=1);

/**
 * @license   MIT License
 * @copyright Copyright (c) 2016 - 2021, Adam Banaszkiewicz
 * @link      https://github.com/requtize/assetter
 */
namespace Requtize\Assetter;

/**
 * @author Adam Banaszkiewicz https://github.com/requtize
 */
interface PluginInterface
{
    /**
     * @param AssetterInterface $assetter
     */
    public function register(AssetterInterface $assetter): void;
}
