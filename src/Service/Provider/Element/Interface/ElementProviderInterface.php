<?php

namespace Factotum\SafeDeleteBundle\Service\Provider\Element\Interface;

interface ElementProviderInterface
{
    /**
     * @param array $paths
     * @return array
     */
    public function getElementsByPaths(array $paths): array;

    /**
     * @param array $ids
     * @return array
     */
    public function getElementsByIds(array $ids): array;

    /**
     * @param string $elementType
     * @return bool
     */
    public function supports(string $elementType): bool;
}
