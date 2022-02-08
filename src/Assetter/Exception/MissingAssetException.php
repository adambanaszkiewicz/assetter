<?php

declare(strict_types=1);

/**
 * @license   MIT License
 * @copyright Copyright (c) 2016 - 2021, Adam Banaszkiewicz
 * @link      https://github.com/requtize/assetter
 */
namespace Requtize\Assetter\Exception;

/**
 * @author Adam Banaszkiewicz https://github.com/requtize
 */
class MissingAssetException extends AssetterException
{
    /**
     * @param string $name
     *
     * @return static
     */
    public static function fromName(string $name, array $alternatives): self
    {
        return new self(sprintf('Asset "%s" not found.' . self::renderAlternatives($alternatives), $name));
    }

    /**
     * @param string $name
     * @param string $dependencyOf
     *
     * @return static
     */
    public static function fromNameAsDependencyOf(string $name, string $dependencyOf, array $alternatives): self
    {
        return new self(sprintf('Asset "%s" not found (a dependency of "%s").' . self::renderAlternatives($alternatives), $name, $dependencyOf));
    }

    private static function renderAlternatives(array $alternatives): string
    {
        if (empty($alternatives)) {
            return '';
        }

        return ' Did you mean one of these: ' . implode(', ', $alternatives);
    }
}
