<?php

declare(strict_types=1);

/**
 * @license   MIT License
 * @copyright Copyright (c) 2016 - 2020, Adam Banaszkiewicz
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
class Collection implements CollectionInterface
{
    /**
     * @var array
     */
    protected $collection = [];

    /**
     * @var string
     */
    protected $defaultGroup = 'def';

    /**
     * @param array $collection
     * @param string $defaultGroup
     */
    public function __construct(array $collection = [], string $defaultGroup = 'body')
    {
        $this->defaultGroup = $defaultGroup;

        $this->replace($collection);
    }

    /**
     * {@inheritdoc}
     */
    public function replace(array $collection): void
    {
        foreach ($collection as $name => $data) {
            $this->append($name, $data);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function append(string $name, array $data): void
    {
        if (isset($data['scripts']) && is_array($data['scripts'])) {
            $data['scripts'] = $this->resolveFilesList($data['scripts'], $data['revision'] ?? null);
        }
        if (isset($data['styles']) && is_array($data['styles'])) {
            $data['styles'] = $this->resolveFilesList($data['styles'], $data['revision'] ?? null);
        }

        $this->collection[$name] = [
            'priority' => $data['priority'] ?? 0,
            'scripts'  => $data['scripts'] ?? [],
            'styles'   => $data['styles'] ?? [],
            'group'    => $data['group'] ?? $this->defaultGroup,
            'require'  => $data['require'] ?? [],
            'included' => $data['included'] ?? [],
        ];
    }

    /**
     * @param array $files
     *
     * @param null $revision
     *
     * @return array
     */
    protected function resolveFilesList(array $files, $revision = null): array
    {
        $result = [];

        foreach ($files as $key => $val) {
            if (is_numeric($key)) {
                if (\is_string($val)) {
                    $result[] = [
                        'file'     => $val,
                        'revision' => $revision,
                    ];
                } elseif(\is_array($val)) {
                    $result[] = [
                        'file'     => $val['file'] ?? null,
                        'revision' => $val['revision'] ?? $revision,
                    ];
                }
            } elseif (is_numeric($val)) {
                if(\is_string($key)) {
                    $result[] = [
                        'file'     => $key,
                        'revision' => $val,
                    ];
                }
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultGroup(): string
    {
        return $this->defaultGroup;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultGroup(string $defaultGroup): void
    {
        $this->defaultGroup = $defaultGroup;
    }

    /**
     * {@inheritdoc}
     */
    public function getNames(): array
    {
        return array_keys($this->collection);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->collection);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset): bool
    {
        return isset($this->collection[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->collection[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value): void
    {
        if ($offset !== null) {
            $this->collection[$offset] = $value;
        } else {
            $this->collection[] = $value;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset): void
    {
        unset($this->collection[$offset]);
    }
}
