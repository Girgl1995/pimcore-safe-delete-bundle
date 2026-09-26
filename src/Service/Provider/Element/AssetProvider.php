<?php

declare(strict_types=1);

namespace Factotum\SafeDeleteBundle\Service\Provider\Element;

use Factotum\SafeDeleteBundle\Service\Provider\Element\Interface\ElementProviderInterface;
use Pimcore\Model\Asset\Listing;
use Factotum\SafeDeleteBundle\SafeDeleteConstants;

class AssetProvider implements ElementProviderInterface
{
    /**
     * @param array $paths
     * @return array
     */
    public function getElementsByPaths(array $paths): array
    {
        $listing = new Listing();
        $listing->setCondition(ElementProviderInterface::CONDITION_PATHS, [$paths]);

        return $listing->getData();
    }

    /**
     * @param mixed $ids
     * @return array
     */
    public function getElementsByIds(array $ids): array
    {
        $listing = new Listing();
        $listing->setCondition(ElementProviderInterface::CONDITION_IDS, [$ids]);

        return $listing->getData();
    }

    /**
     * @param string $elementType
     * @return bool
     */
    public function supports(string $elementType): bool
    {
        return $elementType === SafeDeleteConstants::TYPE_ASSET;
    }
}
