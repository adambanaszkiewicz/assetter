<?php
/**
 * Copyright (c) 2016 - 2018 by Adam Banaszkiewicz
 *
 * @license   MIT License
 * @copyright Copyright (c) 2016 - 2018, Adam Banaszkiewicz
 * @link      https://github.com/requtize/assetter
 */
namespace Requtize\Assetter;

interface PluginInterface
{
    public function register(Assetter $assetter);
}
