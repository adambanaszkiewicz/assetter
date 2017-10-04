<?php

namespace Requtize\Assetter\Plugin\Leafo\LessPhp;

use lessc;

class Compiler extends lessc
{
    public function compileFile($file, $outFname = NULL)
    {
        $content = file_get_contents($file);
        $path    = pathinfo($file, PATHINFO_DIRNAME);

        if(is_dir($path))
            $this->addImportDir($path);

        return $this->compile($content);
    }
}
