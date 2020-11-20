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
     *
     * @return string
     */
    public function all(): string;

    /**
     * Returns CSS tags from given group name.
     * If group name is asterisk (*), will return from all loaded groups.
     *
     * @return string
     */
    public function styles(): string;

    /**
     * Returns JS tags from given group name.
     * If group name is asterisk (*), will return from all loaded groups.
     *
     * @return string
     */
    public function scripts(): string;

    /**
     * @return array
     */
    public function getPayload(): array;

    /**
     * @param array $payload
     */
    public function setPayload(array $payload): void;
}
