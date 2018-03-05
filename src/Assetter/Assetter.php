<?php
/**
 * Copyright (c) 2016 - 2017 by Adam Banaszkiewicz
 *
 * @license   MIT License
 * @copyright Copyright (c) 2016 - 2017, Adam Banaszkiewicz
 * @link      https://github.com/requtize/assetter
 */
namespace Requtize\Assetter;

use Requtize\FreshFile\FreshFile;

/**
 * Asseter class. Manage assets (CSS and JS) for website, and it's
 * dependencies by other assets. Allows load full lib by giving a name
 * or append custom library's files.
 *
 * @author Adam Banaszkiewicz https://github.com/requtize
 */
class Assetter
{
    /**
     * @var Requtize\FreshFile\FreshFile
     */
    protected $freshFile;

    /**
     * Stores collection of libraries to load.
     * @var array
     */
    protected $collection = [];

    /**
     * Stores global revision for all files.
     * Revision number is added automatically to all files as GET aparameter.
     * Allows refresh files from cache in browsers.
     * @var integer
     */
    protected $revision = 0;

    /**
     * Stores name of default group for library.
     * @var string
     */
    protected $defaultGroup = 'def';

    /**
     * Loaded libraries.
     * @var array
     */
    protected $loaded = [];

    /**
     * Store namespaces, which will be replaces when some asset will be loaded.
     * @var array
     */
    protected $namespaces = [];

    /**
     * Store events listeners.
     * @var array
     */
    protected $eventListeners = [];

    /**
     * List of registered plugins.
     * @var array
     */
    protected $plugins = [];

    /**
     * Constructor.
     * @param FreshFile $freshFile
     */
    public function __construct(FreshFile $freshFile)
    {
        $this->freshFile = $freshFile;
    }

    /**
     * While cloning self, clears loaded libraries.
     * @return void
     */
    public function __clone()
    {
        $this->loaded = [];
    }

    /**
     * Return clone of this object, without loaded libraries in it.
     * @return Cloned self object.
     */
    public function doClone()
    {
        return clone $this;
    }

    /**
     * Returns FreshFile object.
     * @return FreshFile
     */
    public function getFreshFile()
    {
        return $this->freshFile;
    }

    /**
     * Registers plugin and add to list.
     * @param  PluginInterface $plugin PluginInterface object.
     * @return self
     */
    public function registerPlugin(PluginInterface $plugin)
    {
        $plugin->register($this);

        $this->plugins[] = $plugin;

        return $this;
    }

    public function getRegisteredPlugins()
    {
        return $this->plugins;
    }

    /**
     * Attaches callable method/function to given event name.
     * @param  string   $event    Event name.
     * @param  callable $callable Callback that is fired when event is triggered.
     * @return self
     */
    public function listenEvent($event, $callable)
    {
        $this->eventListeners[$event][] = $callable;

        return $this;
    }

    /**
     * Fires event with specified arguments. Arguments were passed
     * as next params of function.
     * @param  string $event Event name to fire.
     * @param  array  $args  Array fo args.
     * @return array  Modified arguments.
     */
    public function fireEvent($event, array $args = [])
    {
        if(isset($this->eventListeners[$event]) === false)
            return $args;

        foreach($this->eventListeners[$event] as $listener)
            call_user_func_array($listener, $args);

        return $args;
    }

    /**
     * Register namespace.
     * @param  string $ns  Namespace name.
     * @param  string $def Namespace path.
     * @return self
     */
    public function registerNamespace($ns, $def)
    {
        list($ns, $def) = $this->fireEvent('namespace.register', [ $ns, $def ]);

        $this->namespaces[$ns] = $def;

        return $this;
    }

    /**
     * Unregister namespace.
     * @param  string $ns namespace name.
     * @return self
     */
    public function unregisterNamespace($ns)
    {
        list($ns) = $this->fireEvent('namespace.unregister', [ $ns ]);

        unset($this->namespaces[$ns]);

        return $this;
    }

    /**
     * Gets current default global group for files that have not
     * defined in collection, or in append() array.
     * @return string
     */
    public function getDefaultGroup()
    {
        return $this->defaultGroup;
    }

    /**
     * Sets default group for files.
     * @param string $defaultGroup
     * @return self
     */
    public function setDefaultGroup($defaultGroup)
    {
        list($defaultGroup) = $this->fireEvent('default-group.set', [ $defaultGroup ]);

        $this->defaultGroup = $defaultGroup;

        return $this;
    }

    /**
     * Returns full collection of registered assets.
     * @return array
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * Sets collection of assets.
     * @param  array $collection
     * @return self
     */
    public function setCollection(array $collection)
    {
        list($collection) = $this->fireEvent('collection.set', [ $collection ]);

        foreach($collection as $asset)
        {
            $this->appendToCollection($asset);
        }

        return $this;
    }

    /**
     * Append asset array to collection. before this, apply required
     * indexes if not exists.
     * @param  array $asset $array with asset data.
     * @return self
     */
    public function appendToCollection(array $data)
    {
        list($data) = $this->fireEvent('append-to-collection', [ $data ]);

        $files = [];

        if(isset($data['files']['js']) && is_array($data['files']['js']))
            $files['js'] = $this->resolveFilesList($data['files']['js']);
        if(isset($data['files']['css']) && is_array($data['files']['css']))
            $files['css'] = $this->resolveFilesList($data['files']['css']);

        $this->collection[] = [
            'order'    => isset($data['order']) ? $data['order'] : 0,
            'name'     => isset($data['name'])  ? $data['name']  : uniqid(),
            'files'    => $files,
            'group'    => isset($data['group']) ? $data['group'] : $this->defaultGroup,
            'require'  => isset($data['require']) ? $data['require'] : []
        ];

        return $this;
    }

    /**
     * Loads assets from given name.
     * @param  string $name Name of library/asset.
     * @return self
     */
    public function load($data)
    {
        list($data) = $this->fireEvent('load', [ $data ]);

        if(is_array($data))
        {
            $this->loadFromArray($data);
        }
        else
        {
            $this->loadFromCollection($data);
        }

        return $this;
    }

    /**
     * Loads given asset (by name) from defined collection.
     * @param  string $name Asset name.
     * @return self
     */
    public function loadFromCollection($name)
    {
        if($this->alreadyLoaded($name))
        {
            return $this;
        }

        list($name) = $this->fireEvent('load-from-collection', [ $name ]);

        foreach($this->collection as $item)
        {
            if($item['name'] === $name)
            {
                $this->loadFromArray($item);
            }
        }

        return $this;
    }

    /**
     * Load asset by given array. Apply registered namespaces for all
     * files' paths.
     * @param  array  $item Asset data array.
     * @return self
     */
    public function loadFromArray(array $data)
    {
        list($data) = $this->fireEvent('load-from-array', [ $data ]);

        $files = [];

        if(isset($data['files']['js']) && is_array($data['files']['js']))
            $files['js'] = $this->resolveFilesList($data['files']['js']);
        if(isset($data['files']['css']) && is_array($data['files']['css']))
            $files['css'] = $this->resolveFilesList($data['files']['css']);

        $item = [
            'order'    => isset($data['order']) ? $data['order'] : 0,
            'name'     => isset($data['name']) ? $data['name'] : uniqid(),
            'files'    => $files,
            'group'    => isset($data['group']) ? $data['group'] : $this->defaultGroup,
            'require'  => isset($data['require']) ? $data['require'] : []
        ];

        if(isset($item['files']['js']) && is_array($item['files']['js']))
            $item['files']['js'] = $this->applyNamespaces($item['files']['js']);
        if(isset($item['files']['css']) && is_array($item['files']['css']))
            $item['files']['css'] = $this->applyNamespaces($item['files']['css']);

        $this->loaded[] = $item;

        if(isset($item['require']) && is_array($item['require']))
        {
            foreach($item['require'] as $name)
            {
                $this->loadFromCollection($name);
            }
        }

        return $this;
    }

    /**
     * Check if given library name was already loaded.
     * @param  string $name Name of library/asset.
     * @return boolean
     */
    public function alreadyLoaded($name)
    {
        foreach($this->loaded as $item)
        {
            if($item['name'] === $name)
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns both CSS and JS tags from given group name.
     * If group name is asterisk (*), will return from all loaded groups.
     * @param  string $group Group name.
     * @return string HTML tags as string.
     */
    public function all($group = '*')
    {
        $this->sort();

        $cssList = $this->getLoadedCssList($group);
        $jsList  = $this->getLoadedJsList($group);

        list($cssList, $jsList) = $this->fireEvent('load.all', [ & $cssList, & $jsList ]);

        $cssList = $this->transformListToLinkHtmlNodes($cssList);
        $jsList  = $this->transformListToScriptHtmlNodes($jsList);

        return implode("\n", $cssList)."\n".implode("\n", $jsList);
    }

    /**
     * Returns CSS tags from given group name.
     * If group name is asterisk (*), will return from all loaded groups.
     * @param  string $group Group name.
     * @return string HTML tags as string.
     */
    public function css($group = '*')
    {
        $this->sort();

        $cssList = $this->getLoadedCssList($group);

        list($cssList) = $this->fireEvent('load.css', [ & $cssList ]);

        $cssList = $this->transformListToLinkHtmlNodes($cssList);

        return implode("\n", $cssList);
    }

    /**
     * Returns JS tags from given group name.
     * If group name is asterisk (*), will return from all loaded groups.
     * @param  string $group Group name.
     * @return string HTML tags as string.
     */
    public function js($group = '*')
    {
        $this->sort();

        $jsList = $this->getLoadedJsList($group);

        list($jsList) = $this->fireEvent('load.js', [ & $jsList ]);

        $jsList = $this->transformListToScriptHtmlNodes($jsList);

        return implode("\n", $jsList);
    }

    protected function resolveFilesList(array $files)
    {
        $result = [];

        foreach($files as $key => $val)
        {
            if(is_numeric($key) && is_string($val))
            {
                $result[] = [
                    'file'     => $val,
                    'revision' => null
                ];
            }
            elseif(is_numeric($key) && is_array($val))
            {
                $result[] = [
                    'file'     => isset($val['file']) ? $val['file'] : null,
                    'revision' => isset($val['revision']) ? $val['revision'] : null
                ];
            }
            elseif(is_string($key) && is_numeric($val))
            {
                $result[] = [
                    'file'     => $key,
                    'revision' => $val
                ];
            }
        }

        return $result;
    }

    protected function applyNamespaces(array $files)
    {
        foreach($files as $key => $file)
        {
            $files[$key] = str_replace(array_keys($this->namespaces), array_values($this->namespaces), $file);
        }

        return $files;
    }

    protected function resolveGroup($group, $type)
    {
        if($group == null)
        {
            return $this->defaultGroup;
        }

        return $group;
    }

    protected function getLoadedCssList($group)
    {
        $group = $this->resolveGroup($group, 'css');

        $result = [];

        foreach($this->loaded as $item)
        {
            if($group != '*')
            {
                if($item['group'] != $group)
                {
                    continue;
                }
            }

            if(isset($item['files']['css']) && is_array($item['files']['css']))
            {
                $result[] = [
                    'files' => $item['files']['css']
                ];
            }
        }

        return $this->arrayUnique($result);
    }

    protected function transformListToLinkHtmlNodes(array $list)
    {
        $result = [];

        foreach($list as $group)
        {
            foreach($group['files'] as $file)
            {
                $result[] = '<link rel="stylesheet" type="text/css" href="'.$file['file'].($file['revision'] ? '?rev='.$file['revision'] : '').'" />';
            }
        }

        return $result;
    }

    protected function getLoadedJsList($group)
    {
        $group = $this->resolveGroup($group, 'js');

        $result = [];

        foreach($this->loaded as $item)
        {
            if($group != '*')
            {
                if($item['group'] != $group)
                {
                    continue;
                }
            }

            if(isset($item['files']['js']) && is_array($item['files']['js']))
            {
                $result[] = [
                    'files' => $item['files']['js']
                ];
            }
        }

        return $this->arrayUnique($result);
    }

    protected function transformListToScriptHtmlNodes(array $list)
    {
        $result = [];

        foreach($list as $group)
        {
            foreach($group['files'] as $file)
            {
                $result[] = '<script src="'.$file['file'].($file['revision'] ? '?rev='.$file['revision'] : '').'"></script>';
            }
        }

        return $result;
    }

    protected function arrayUnique(array $list)
    {
        $filesLoaded = [];

        foreach($list as $gk => $group)
        {
            foreach($group['files'] as $fk => $file)
            {
                if(in_array($file['file'], $filesLoaded))
                {
                    unset($list[$gk]['files'][$fk]);
                    continue;
                }

                $filesLoaded[] = $file['file'];
            }
        }

        return $list;
    }

    protected function sort()
    {
        array_multisort($this->loaded);
    }
}
