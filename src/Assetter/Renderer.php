<?php

declare(strict_types=1);

/**
 * @license   MIT License
 * @copyright Copyright (c) 2016 - 2020, Adam Banaszkiewicz
 * @link      https://github.com/requtize/assetter
 */
namespace Requtize\Assetter;

/**
 * @author Adam Banaszkiewicz https://github.com/requtize
 */
class Renderer implements RendererInterface
{
    /**
     * @var array
     */
    protected $payload = [];

    /**
     * @param iterable $payload
     */
    public function __construct(iterable $payload)
    {
        $this->payload = $payload;
    }

    /**
     * {@inheritdoc}
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    /**
     * {@inheritdoc}
     */
    public function setPayload(array $payload): void
    {
        $this->payload = $payload;
    }

    /**
     * {@inheritdoc}
     */
    public function all(): string
    {
        return $this->styles()."\n".$this->scripts();
    }

    /**
     * {@inheritdoc}
     */
    public function styles(): string
    {
        $cssList = $this->getStylesList();
        $cssList = $this->transformListToLinkHtmlNodes($cssList);

        return implode("\n", $cssList);
    }

    /**
     * {@inheritdoc}
     */
    public function scripts(): string
    {
        $jsList = $this->getScriptsList();
        $jsList = $this->transformListToScriptHtmlNodes($jsList);

        return implode("\n", $jsList);
    }

    /**
     * @return array
     */
    protected function getStylesList(): array
    {
        $result = [];

        foreach ($this->payload as $item) {
            if ($item['styles'] !== []) {
                $result[] = [
                    'files' => $item['styles']
                ];
            }
        }

        return $this->arrayUnique($result);
    }

    /**
     * @param array $list
     *
     * @return array
     */
    protected function transformListToLinkHtmlNodes(array $list): array
    {
        $result = [];

        foreach ($list as $group) {
            foreach ($group['files'] as $file) {
                $link = $file['file'];

                if ($file['revision']) {
                    if (strpos($file['file'], '?') === false) {
                        $link .= '?rev=' . $file['revision'];
                    } else {
                        $link .= '&rev=' . $file['revision'];
                    }
                }

                $result[] = '<link rel="stylesheet" type="text/css" href="'.$link.'" />';
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function getScriptsList(): array
    {
        $result = [];

        foreach ($this->payload as $item) {
            if ($item['scripts'] !== []) {
                $result[] = [
                    'files' => $item['scripts']
                ];
            }
        }

        return $this->arrayUnique($result);
    }

    /**
     * @param array $list
     *
     * @return array
     */
    protected function transformListToScriptHtmlNodes(array $list): array
    {
        $result = [];

        foreach ($list as $group) {
            foreach ($group['files'] as $file) {
                $link = $file['file'];

                if ($file['revision']) {
                    if (strpos($file['file'], '?') === false) {
                        $link .= '?rev=' . $file['revision'];
                    } else {
                        $link .= '&rev=' . $file['revision'];
                    }
                }

                $result[] = '<script src="'.$link.'"></script>';
            }
        }

        return $result;
    }

    /**
     * @param array $list
     *
     * @return array
     */
    protected function arrayUnique(array $list): array
    {
        $filesLoaded = [];

        foreach ($list as $gk => $group) {
            foreach ($group['files'] as $fk => $file) {
                if (\in_array($file['file'], $filesLoaded, true)) {
                    unset($list[$gk]['files'][$fk]);
                    continue;
                }

                $filesLoaded[] = $file['file'];
            }
        }

        return $list;
    }
}
