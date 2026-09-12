<?php

declare(strict_types=1);

namespace Factotum\SafeDeleteBundle\Service\Provider\Dependency\Utils;

use Pimcore\Model\Element\AbstractElement;

class DependencyProviderUtils
{
    /**
     * @param AbstractElement $object
     * @param array $result
     * @return string
     */
    public static function generateKey(AbstractElement $object, array $result): string
    {
        $objectKey = $object->getKey();

        if (!array_key_exists($objectKey, $result)) {
            return $objectKey;
        }

        $count = 0;
        foreach ($result as $key => $value) {
            if ($objectKey === $key) {
                $count++;
            }
        }

        return $key .= $count;
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    public static function getClassName(AbstractElement $element): string
    {
        $namesSpace = get_class($element);
        if ($pos = strrpos($namesSpace, '\\')) {
            return substr($namesSpace, $pos + 1);
        }

        return '';
    }
}
