<?php

declare(strict_types=1);

namespace Factotum\SafeDeleteBundle\Config;

class SafeDeleteConfig
{
    private const CONFIG_CHILDREN_KEY = 'include_children';

    /**
     * @param array $config
     */
    public function __construct(private readonly array $config)
    {
    }

    /**
     * @return bool
     */
    public function getIncludeChildren(): bool
    {
        return $this->config[self::CONFIG_CHILDREN_KEY];
    }
}
