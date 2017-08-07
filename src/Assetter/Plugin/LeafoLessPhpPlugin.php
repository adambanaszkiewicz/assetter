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
    protected $filesRoot = null;

    public function __construct($filesRoot)
    {
        $this->filesRoot = $filesRoot;
        $this->less = new lessc;
        $this->less->setFormatter('compressed');
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
        $filepathNew = str_replace('.less', '.css', $filepath);

        $this->less->checkedCompile($this->filesRoot.$filepath, $this->filesRoot.$filepathNew);

        return $filepathNew;
    }
}
