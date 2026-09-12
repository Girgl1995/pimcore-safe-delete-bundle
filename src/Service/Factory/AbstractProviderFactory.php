<?php

declare(strict_types=1);

namespace Factotum\SafeDeleteBundle\Service\Factory;

use Factotum\SafeDeleteBundle\Service\Interface\ProviderInterface;

abstract class AbstractProviderFactory
{
    private iterable $providers;

    /**
     * @param iterable $providers
     */
    public function __construct(iterable $providers)
    {
        $this->providers = $providers;
    }

    /**
     * @param string $elementType
     * @return ProviderInterface
     */
    public function getProvider(string $elementType): ProviderInterface
    {
        foreach ($this->providers as $provider) {
            if ($provider->supports($elementType)) {
                return $provider;
            }
        }
    }
}
