<?php

namespace Factotum\SafeDeleteBundle\Service\Interface;

interface ProviderInterface
{
    public const TYPE_ASSET = 'asset';
    public const TYPE_DOCUMENT = 'document';
    public const TYPE_OBJECT = 'object';

    /**
     * @param string $elementType
     * @return bool
     */
    public function supports(string $elementType): bool;
}
