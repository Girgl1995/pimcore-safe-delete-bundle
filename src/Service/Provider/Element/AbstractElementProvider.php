<?php

declare(strict_types=1);

namespace Factotum\SafeDeleteBundle\Service\Provider\Element;

use Factotum\SafeDeleteBundle\Service\Provider\Element\Interface\ElementProviderInterface;
use Pimcore\Model\Listing\AbstractListing;

abstract class AbstractElementProvider implements ElementProviderInterface
{
    protected const CONDITION_IDS             = '`id` IN (?)';
    protected const CONDITION_PATHS           = '`path` LIKE (?)';
    protected const SQL_OR_OPERATOR           = 'OR';
    protected const SQL_LIKE_WILDCARD_PERCENT = '/%';
    protected const CONDITION_SEPERATOR       = ' ';

    /**
     * @param AbstractListing $listing
     * @param array $paths
     * @return array
     */
    protected function buildPathsCondition(AbstractListing $listing, array $paths): AbstractListing
    {
        $pathsMaxIndex = count($paths) - 1;

        $condition       = '';
        $conditionValues = [];
        for ($i = 0; $i <= $pathsMaxIndex; $i++) {
            $conditionValues[] = $paths[$i] . self::SQL_LIKE_WILDCARD_PERCENT;

            if ($i === $pathsMaxIndex) {
                $condition .= self::CONDITION_PATHS;
                break;
            }

            $condition .= self::CONDITION_PATHS . self::CONDITION_SEPERATOR . self::SQL_OR_OPERATOR . self::CONDITION_SEPERATOR;
        }

        $listing->setCondition($condition, $conditionValues);

        return $listing;
    }
}
