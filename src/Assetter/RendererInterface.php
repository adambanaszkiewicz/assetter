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
interface RendererInterface
{
    /**
     * Returns both CSS and JS tags from given group name.
     * If group name is asterisk (*), will return from all loaded groups.
     */
    public function all(): string;

    /**
     * Returns CSS tags from given group name.
     * If group name is asterisk (*), will return from all loaded groups.
     */
    public function styles(): string;

    /**
     * Returns JS tags from given group name.
     * If group name is asterisk (*), will return from all loaded groups.
     */
    public function scripts(): string;

    public function getPayload(): array;

    public function setPayload(array $payload): void;

    /**
     * Collect and return array of scripts' links in this build.
     */
    public function collectScripts(): array;

    /**
     * Collect and return array of styles' links in this build.
     */
    public function collectStyles(): array;
}
