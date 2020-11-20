<?php

declare(strict_types=1);

/**
 * @license   MIT License
 * @copyright Copyright (c) 2016 - 2020, Adam Banaszkiewicz
 * @link      https://github.com/requtize/assetter
 */
namespace Requtize\Assetter;

use Requtize\Assetter\Exception\MissingAssetException;

/**
 * @author Adam Banaszkiewicz https://github.com/requtize
 */
interface AssetterInterface
{
    /**
     * @param string $name
     * @param string $path
     */
    public function registerNamespace(string $name, string $path): void;

    /**
     * @param string $name
     */
    public function unregisterNamespace(string $name): void;

    public function clearNamespaces(): void;

    /**
     * @return iterable
     */
    public function getNamespaces(): iterable;

    /**
     * Registers plugin and add to list.
     *
     * @param PluginInterface $plugin
     *
     * @return void
     */
    public function registerPlugin(PluginInterface $plugin): void;

    /**
     * @return iterable
     */
    public function getRegisteredPlugins(): iterable;

    /**
     * Attaches callable method/function to given event name.
     *
     * @param string $event
     * @param callable $callable
     *
     * @return void
     */
    public function listenEvent(string $event, callable $callable): void;

    /**
     * Fires event with specified arguments. Arguments were passed
     * as next params of function.
     *
     * @param string $event
     * @param array  $args
     *
     * @return array  Modified arguments.
     */
    public function fireEvent($event, array $args = []): array;

    /**
     * @return CollectionInterface
     */
    public function getCollection(): CollectionInterface;

    /**
     * Sets collection of assets.
     *
     * @param CollectionInterface $collection
     *
     * @return void
     */
    public function setCollection(CollectionInterface $collection): void;

    /**
     * Loads assets from given name.
     *
     * @param array $names
     *
     * @return void
     *
     * @throws MissingAssetException
     */
    public function require(...$names): void;

    /**
     * @param mixed ...$names
     */
    public function unrequire(...$names): void;

    /**
     * @return array
     */
    public function getRequired(): array;

    /**
     * @param string $group
     *
     * @return RendererInterface
     *
     * @throws MissingAssetException
     */
    public function build(string $group = '*'): RendererInterface;
}
