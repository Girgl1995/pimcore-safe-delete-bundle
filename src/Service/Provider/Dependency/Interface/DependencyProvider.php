<?php

namespace Factotum\SafeDeleteBundle\Service\Provider\Dependency\Interface;

use Factotum\SafeDeleteBundle\Service\Interface\ProviderInterface;
use Pimcore\Model\Element\AbstractElement;

interface DependencyProvider extends ProviderInterface
{
    /**
     * @param array|null $objects
     * @return array
     */
    public function getDependencies(?array $objects): array;

    /**
     * @param AbstractElement $object
     * @return array
     */
    public function getElementDependencies(AbstractElement $object): array;
}
