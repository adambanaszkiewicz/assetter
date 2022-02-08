<?php

declare(strict_types=1);

/**
 * @license   MIT License
 * @copyright Copyright (c) 2016 - 2021, Adam Banaszkiewicz
 * @link      https://github.com/requtize/assetter
 */
namespace Requtize\Assetter;

/**
 * Asseter class. Manage assets (CSS and JS) for website, and it's
 * dependencies by other assets. Allows load full lib by giving a name
 * or append custom library's files.
 *
 * @author Adam Banaszkiewicz https://github.com/requtize
 */
interface CollectionInterface extends \IteratorAggregate, \ArrayAccess
{
    /**
     * Append asset array to collection. before this, apply required
     * indexes if not exists.
     */
    public function append(string $name, array $data): void;

    public function replace(array $data): void;

    /**
     * Gets current default global group for files that have not
     * defined in collection, or in append() array.
     */
    public function getDefaultGroup(): string;

    /**
     * Sets default group for files.
     */
    public function setDefaultGroup(string $defaultGroup): void;

    public function getNames(): array;

    /**
     * Checks if any fo the registered assets contains in the $name group.
     */
    public function hasCollection(string $name): bool;

    /**
     * Returns all assets registered for the $name group.
     */
    public function getNamesFromCollection(string $name): array;
}
