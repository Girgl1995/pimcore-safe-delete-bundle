<?php

declare(strict_types=1);

namespace Factotum\SafeDeleteBundle\Service\Provider\Element\Factory;

use Factotum\SafeDeleteBundle\Service\Provider\Element\Interface\ElementProviderInterface;

class ElementProviderFactory
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
     * @return ElementProviderInterface|null
     */
    public function getProvider(string $elementType): ?ElementProviderInterface
    {
        foreach ($this->providers as $provider) {
            if ($provider->supports($elementType)) {
                return $provider;
            }
        }

        return null;
    }
}
