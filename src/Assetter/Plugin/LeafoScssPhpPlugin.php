<?php
/**
 * Copyright (c) 2016 - 2017 by Adam Banaszkiewicz
 *
 * @license   MIT License
 * @copyright Copyright (c) 2016 - 2017, Adam Banaszkiewicz
 * @link      https://github.com/requtize/assetter
 */
namespace Requtize\Assetter\Plugin;

use scssc;
use Requtize\Assetter\Assetter;
use Requtize\Assetter\PluginInterface;

class LeafoScssPhpPlugin implements PluginInterface
{
    protected $filesRoot = null;

    public function __construct($filesRoot)
    {
        $this->filesRoot = $filesRoot;
        $this->scss = new scssc;
        $this->scss->setFormatter('scss_formatter_compressed');
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
                if(substr($file, -5, 5) === '.scss')
                {
                    $groups[$kg]['files'][$key] = $this->compile($file);
                }
            }
        }
    }

    public function compile($filepath)
    {
        $filepathNew = str_replace('.scss', '.css', $filepath);

        $css = $this->scss->compile(file_get_contents($this->filesRoot.$filepath));

        file_put_contents($this->filesRoot.$filepathNew, $css);

        return $filepathNew;
    }
}
