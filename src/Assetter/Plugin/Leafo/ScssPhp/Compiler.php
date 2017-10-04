<?php

namespace Requtize\Assetter\Plugin\Leafo\ScssPhp;

use Leafo\ScssPhp\Compiler as BaseCompiler;

class Compiler extends BaseCompiler
{
    public function compileFile($file)
    {
        $content = file_get_contents($file);
        $path    = pathinfo($file, PATHINFO_DIRNAME);

        if(is_dir($path))
            $this->addImportPath($path);

        return $this->compile($content);
    }
}
