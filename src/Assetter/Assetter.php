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
 * Asseter class. Manage assets (CSS and JS) for website, and it's
 * dependencies by other assets. Allows load full lib by giving a name
 * or append custom library's files.
 *
 * @author Adam Banaszkiewicz https://github.com/requtize
 */
class Assetter implements AssetterInterface
{
    /**
     * @var CollectionInterface
     */
    protected $collection;

    /**
     * List of names that was required directly using require() method.
     *
     * @var array
     */
    protected $required = [];

    /**
     * List of names that was rendered already. These names are
     * used for the second and next builds, to not load the same
     * assets multiple times.
     *
     * @var array
     */
    protected $rendered = [];

    /**
     * @var array
     */
    protected $eventListeners = [];

    /**
     * @var array
     */
    protected $plugins = [];

    /**
     * @var array
     */
    protected $namespaces = [];

    /**
     * @param CollectionInterface|null $collection
     */
    public function __construct(CollectionInterface $collection = null)
    {
        $this->collection = $collection ?? new Collection();
    }

    /**
     * @inheritDoc
     */
    public function registerNamespace(string $name, string $path): void
    {
        $this->namespaces[$name] = $path;
    }

    /**
     * {@inheritdoc}
     */
    public function unregisterNamespace(string $name): void
    {
        unset($this->namespaces[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function clearNamespaces(): void
    {
        $this->namespaces = [];
    }

    /**
     * {@inheritdoc}
     */
    public function getNamespaces(): iterable
    {
        return $this->namespaces;
    }

    /**
     * {@inheritdoc}
     */
    public function registerPlugin(PluginInterface $plugin): void
    {
        $plugin->register($this);

        $this->plugins[] = $plugin;
    }

    /**
     * {@inheritdoc}
     */
    public function getRegisteredPlugins(): iterable
    {
        return $this->plugins;
    }

    /**
     * {@inheritdoc}
     */
    public function listenEvent(string $event, callable $callable): void
    {
        $this->eventListeners[$event][] = $callable;
    }

    /**
     * {@inheritdoc}
     */
    public function fireEvent($event, array $args = []): array
    {
        if (isset($this->eventListeners[$event]) === false) {
            return $args;
        }

        foreach ($this->eventListeners[$event] as $listener) {
            \call_user_func_array($listener, $args);
        }

        return $args;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection(): CollectionInterface
    {
        return $this->collection;
    }

    /**
     * {@inheritdoc}
     */
    public function setCollection(CollectionInterface $collection): void
    {
        [$this->collection] = $this->fireEvent('collection.set', [$collection]);
    }

    /**
     * {@inheritdoc}
     */
    public function getRequired(): array
    {
        return $this->required;
    }

    /**
     * {@inheritdoc}
     */
    public function require(...$names): void
    {
        // Merge multiple arrays
        if (\is_array($names[0])) {
            $names = array_merge(...$names);
        }

        [$names] = $this->fireEvent('require', [$names]);

        foreach ($names as $name) {
            if (isset($this->collection[$name]) === false) {
                throw MissingAssetException::fromName($name, $this->findAlternatives($name));
            }

            if (\in_array($name, $this->required, true)) {
                continue;
            }

            $this->required[] = $name;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function unrequire(...$names): void
    {
        [$names] = $this->fireEvent('unrequire', [$names]);

        $this->required = array_values(array_diff($this->required, $names));
    }

    /**
     * {@inheritdoc}
     */
    public function build(string $group = '*'): RendererInterface
    {
        $required = $this->required;
        $included = $this->collectIncluded($required);
        $required = $this->recursiveRequire($required, $included);
        $required = $this->collect($required);

        $toRender = $this->filterByGroup($required, $group);
        $toRender = $this->removeRendered($toRender);

        $this->rendered = array_merge($this->rendered, array_keys($toRender));

        $renderer = new Renderer($toRender);

        [$renderer] = $this->fireEvent('build', [& $renderer]);

        return $renderer;
    }

    /**
     * @param array $names
     *
     * @return array
     */
    private function collect(array $names): array
    {
        $payload = [];

        foreach ($names as $name) {
            $asset = $this->collection[$name];

            $payload[$name] = [
                'priority' => $asset['priority'],
                'name'     => $name,
                'scripts'  => $this->applyNamespaces($asset['scripts']),
                'styles'   => $this->applyNamespaces($asset['styles']),
                'group'    => $asset['group'],
                'included' => $asset['included'],
            ];
        }

        uasort($payload, function ($a, $b) {
            return $b['priority'] <=> $a['priority'];
        });

        [$payload] = $this->fireEvent('collect', [& $payload]);

        return $payload;
    }

    /**
     * @param array $files
     *
     * @return array
     */
    protected function applyNamespaces(array $files): array
    {
        foreach ($files as $key => $file) {
            $files[$key] = str_replace(array_keys($this->namespaces), array_values($this->namespaces), $file);
        }

        return $files;
    }

    /**
     * @param array $required
     * @param string $group
     *
     * @return array
     */
    private function filterByGroup(array $required, string $group): array
    {
        $group = $this->resolveGroup($group);

        $result = [];

        foreach ($required as $name => $item) {
            if ($group !== '*' && $item['group'] !== $group) {
                continue;
            }

            $result[$name] = $item;
        }

        return $result;
    }

    /**
     * @param $group
     *
     * @return string
     */
    protected function resolveGroup($group): string
    {
        if ($group === null) {
            return $this->collection->getDefaultGroup();
        }

        return $group;
    }

    /**
     * @param array $required
     *
     * @return array
     */
    private function removeRendered(array $required): array
    {
        foreach ($required as $name => $item) {
            if (\in_array($name, $this->rendered, true)) {
                unset($required[$name]);
            }
        }

        return $required;
    }

    /**
     * @param array $require
     *
     * @return array
     *
     * @throws MissingAssetException
     */
    private function recursiveRequire(array $require, array $skip): array
    {
        $newRequired = [];

        foreach ($require as $sourceName) {
            if (\in_array($sourceName, $skip, true)) {
                continue;
            }

            if (isset($this->collection[$sourceName]) === false) {
                throw MissingAssetException::fromName($sourceName, $this->findAlternatives($sourceName));
            }

            foreach ($this->collection[$sourceName]['require'] as $name) {
                if (\in_array($name, $skip, true)) {
                    continue;
                }

                if (\in_array($name, $require, true)) {
                    continue;
                }

                if (isset($this->collection[$name])) {
                    $newRequired[] = $name;
                } else {
                    throw MissingAssetException::fromNameAsDependencyOf($name, $sourceName, $this->findAlternatives($name));
                }
            }
        }

        if ($newRequired === []) {
            return $require;
        }

        $require = array_unique(array_merge($newRequired, $require));

        return $this->recursiveRequire($require, $skip);
    }

    private function collectIncluded(array $require): array
    {
        $included = [];

        foreach ($require as $name) {
            $included[] = $this->collection[$name]['included'];
        }

        if (empty($included)) {
            return $included;
        }

        $included = array_merge(...$included);

        return $included;
    }

    private function findAlternatives(string $name): array
    {
        $similar = [];

        foreach ($this->collection->getNames() as $alternative) {
            similar_text($name, $alternative, $percent);

            if ($percent > 70) {
                $similar[] = $alternative;
            }
        }

        return $similar;
    }
}
