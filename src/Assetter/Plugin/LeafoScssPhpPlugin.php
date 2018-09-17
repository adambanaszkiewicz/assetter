<?php
/**
 * Copyright (c) 2016 - 2018 by Adam Banaszkiewicz
 *
 * @license   MIT License
 * @copyright Copyright (c) 2016 - 2018, Adam Banaszkiewicz
 * @link      https://github.com/requtize/assetter
 */
namespace Requtize\Assetter\Plugin;

use Leafo\ScssPhp\Formatter\Crunched;
use Requtize\Assetter\Assetter;
use Requtize\Assetter\PluginInterface;
use Requtize\Assetter\Plugin\Leafo\ScssPhp\Compiler;

class LeafoScssPhpPlugin implements PluginInterface
{
    protected $filesRoot;
    protected $freshFile;
    protected $scss;

    public function __construct($filesRoot)
    {
        $this->filesRoot = $filesRoot;
    }

    public function register(Assetter $assetter)
    {
        $this->freshFile = $assetter->getFreshFile();

        $assetter->listenEvent('load.all', [ $this, 'replaceAndCompile' ]);
        $assetter->listenEvent('load.css', [ $this, 'replaceAndCompile' ]);
    }

    public function replaceAndCompile(array & $groups)
    {
        foreach($groups as $kg => $group)
        {
            foreach($group['files'] as $key => $file)
            {
                if(substr($file['file'], -5, 5) === '.scss')
                {
                    $groups[$kg]['files'][$key]['file']     = $this->compile($file['file']);
                    $groups[$kg]['files'][$key]['revision'] = $this->freshFile->getFilemtimeMetadata($this->filesRoot.$file['file']);
                }
            }
        }
    }

    public function compile($filepath)
    {
        $filepathRoot = $this->filesRoot.$filepath;
        $filepathNew  = str_replace('.scss', '.css', $filepath);

        if($this->freshFile->isFresh($filepathRoot))
        {
            $this->preparePlugin();

            $css = $this->scss->compileFile($filepathRoot);

            $this->freshFile->setRelatedFiles($filepathRoot, array_keys($this->scss->getParsedFiles()));

            file_put_contents($this->filesRoot.$filepathNew, $css);
        }

        return $filepathNew;
    }

    protected function preparePlugin()
    {
        if($this->scss)
            return;

        $this->scss = new Compiler;
        $this->scss->setLineNumberStyle(Compiler::LINE_COMMENTS);
        $this->scss->setFormatter(Crunched::class);
    }
}
