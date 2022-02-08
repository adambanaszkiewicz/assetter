<?php

declare(strict_types=1);

/**
 * @license   MIT License
 * @copyright Copyright (c) 2016 - 2021, Adam Banaszkiewicz
 * @link      https://github.com/requtize/assetter
 */
namespace Requtize\Assetter;

/**
 * @author Adam Banaszkiewicz https://github.com/requtize
 */
class Renderer implements RendererInterface
{
    protected array $payload = [];

    public function __construct(iterable $payload)
    {
        $this->payload = $payload;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function setPayload(array $payload): void
    {
        $this->payload = $payload;
    }

    public function all(): string
    {
        return $this->styles()."\n".$this->scripts();
    }

    public function styles(): string
    {
        $cssList = $this->getStylesList();
        $cssList = $this->transformListToLinkHtmlNodes($cssList);

        return implode("\n", $cssList);
    }

    public function scripts(): string
    {
        $jsList = $this->getScriptsList();
        $jsList = $this->transformListToScriptHtmlNodes($jsList);

        return implode("\n", $jsList);
    }

    public function collectScripts(): array
    {
        return $this->getScriptsList();
    }

    public function collectStyles(): array
    {
        return $this->getStylesList();
    }

    protected function getStylesList(): array
    {
        $result = [];

        foreach ($this->payload as $item) {
            if ($item['styles'] !== []) {
                $result[] = array_map(function (array $file) {
                    return $this->prepareLinkWithRevision($file);
                }, $item['styles']);
            }
        }

        return array_merge(...$this->arrayUnique($result));
    }

    protected function transformListToLinkHtmlNodes(array $list): array
    {
        $result = [];

        foreach ($list as $file) {
            $result[] = '<link rel="stylesheet" type="text/css" href="' . $file . '" />';
        }

        return $result;
    }

    protected function getScriptsList(): array
    {
        $result = [];

        foreach ($this->payload as $item) {
            if ($item['scripts'] !== []) {
                $result[] = array_map(function (array $file) {
                    return $this->prepareLinkWithRevision($file);
                }, $item['scripts']);
            }
        }

        return array_merge(...$this->arrayUnique($result));
    }

    protected function transformListToScriptHtmlNodes(array $list): array
    {
        $result = [];

        foreach ($list as $file) {
            $result[] = '<script src="' . $file . '"></script>';
        }

        return $result;
    }

    protected function arrayUnique(array $list): array
    {
        $filesLoaded = [];

        foreach ($list as $gk => $group) {
            foreach ($group as $fk => $file) {
                if (\in_array($file, $filesLoaded, true)) {
                    unset($list[$gk][$fk]);
                    continue;
                }

                $filesLoaded[] = $file;
            }
        }

        return $list;
    }

    private function prepareLinkWithRevision(array $file): string
    {
        $link = $file['file'];

        if ($file['revision']) {
            if (strpos($file['file'], '?') === false) {
                $link .= '?rev=' . $file['revision'];
            } else {
                $link .= '&rev=' . $file['revision'];
            }
        }

        return $link;
    }
}
