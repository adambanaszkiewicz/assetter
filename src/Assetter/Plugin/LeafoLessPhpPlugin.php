<?php
/**
 * Copyright (c) 2016 - 2017 by Adam Banaszkiewicz
 *
 * @license   MIT License
 * @copyright Copyright (c) 2016 - 2017, Adam Banaszkiewicz
 * @link      https://github.com/requtize/assetter
 */
namespace Requtize\Assetter\Plugin;

use lessc;
use Requtize\Assetter\Assetter;
use Requtize\Assetter\PluginInterface;

class LeafoLessPhpPlugin implements PluginInterface
{
    const CACHE_FILENAME = 'assetter.leafo-less.serialize';

    protected $filesRoot;
    protected $cacheDir;
    protected $cacheData = [];
    protected $cacheNeedRefresh = false;

    public function __construct($filesRoot, $cacheDir = null)
    {
        $this->filesRoot = $filesRoot;
        $this->setCacheDir($cacheDir ?: __DIR__.'/../../../cache');
        $this->less = new lessc;
        $this->less->setFormatter('compressed');
    }

    public function __destruct()
    {
        if($this->cacheNeedRefresh)
        {
            file_put_contents($this->cacheDir.'/'.self::CACHE_FILENAME, serialize($this->cacheData));
        }
    }

    public function setCacheDir($cacheDir)
    {
        $this->cacheDir = $cacheDir;

        if(is_dir($this->cacheDir) === false)
        {
            mkdir($this->cacheDir, 0777, true);
        }

        if(is_file($this->cacheDir.'/'.self::CACHE_FILENAME))
        {
            $data = file_get_contents($this->cacheDir.'/'.self::CACHE_FILENAME);

            @ $unserialized = (array) unserialize($data);

            if(is_array($unserialized))
            {
                $this->cacheData = $unserialized;
            }
        }

        return $this;
    }

    public function register(Assetter $assetter)
    {
        $assetter->listenEvent('load.all', [ $this, 'replaceAndCompile' ]);
        $assetter->listenEvent('load.css', [ $this, 'replaceAndCompile' ]);
    }

    public function replaceAndCompile(array & $groups)
    {
        foreach($groups as $kg => $group)
        {
            foreach($group['files'] as $key => $file)
            {
                if(substr($file, -5, 5) === '.less')
                {
                    $groups[$kg]['files'][$key] = $this->compile($file);
                }
            }
        }
    }

    public function compile($filepath)
    {
        $filepathRoot = $this->filesRoot.$filepath;
        $filepathNew  = str_replace('.less', '.css', $filepath);

        if($this->isFileFresh($filepathRoot) === false)
        {
            $css = $this->less->compile(file_get_contents($filepathRoot));

            file_put_contents($this->filesRoot.$filepathNew, $css);

            $this->cacheData[$filepathRoot] = filemtime($filepathRoot);
            $this->cacheNeedRefresh = true;
        }

        return $filepathNew;
    }

    public function isFileFresh($filepath)
    {
        if(isset($this->cacheData[$filepath]) === false)
            return false;

        return $this->cacheData[$filepath] >= filemtime($filepath);
    }
}
