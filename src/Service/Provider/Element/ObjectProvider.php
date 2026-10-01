<?php

declare(strict_types=1);

namespace Factotum\SafeDeleteBundle\Service\Provider\Element;

use Factotum\SafeDeleteBundle\Service\Provider\Element\AbstractElementProvider;
use Factotum\SafeDeleteBundle\SafeDeleteConstants;
use Pimcore\Model\DataObject\Listing;

class ObjectProvider extends AbstractElementProvider
{
    /**
     * @param array $paths
     * @return array
     */
    public function getElementsByPaths(array $paths): array
    {
        $listing = new Listing();

        $listing = $this->buildPathsCondition($listing, $paths);

        return $listing->getData();
    }

    /**
     * @param mixed $ids
     * @return array
     */
    public function getElementsByIds(array $ids): array
    {
        $listing = new Listing();
        $listing->setCondition(parent::CONDITION_IDS, [$ids]);

        return $listing->getData();
    }

    /**
     * @param string $elementType
     * @return bool
     */
    public function supports(string $elementType): bool
    {
        return $elementType === SafeDeleteConstants::TYPE_OBJECT;
    }
}
