<?php

namespace Factotum\SafeDeleteBundle\Service\Provider\Interface;

interface ProviderInterface
{
    /**
     * @param string $elementType
     * @return bool
     */
    public function supports(string $elementType): bool;
}
