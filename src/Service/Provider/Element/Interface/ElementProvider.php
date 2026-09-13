<?php

namespace Factotum\SafeDeleteBundle\Service\Provider\Element\Interface;

use Factotum\SafeDeleteBundle\Service\Provider\Interface\ProviderInterface;

interface ElementProvider extends ProviderInterface
{
    public const CONDITION_IDS = '`id` IN (?)';
    public const CONDITION_PATHS = '`path` IN (?)';

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
}
