<?php

declare(strict_types=1);

namespace Factotum\SafeDeleteBundle\Service\Provider\Dependency\Utils;

use Factotum\SafeDeleteBundle\SafeDeleteConstants;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\Document;
use Pimcore\Model\Element\AbstractElement;

class ElementDependencyProviderUtils
{
    /**
     * @param array $elements
     * @param AbstractElement $object
     * @return string
     */
    public static function generateUniqueArrayKeyForElement(array $elements, AbstractElement $object): string
    {
        $elementKey = $object->getKey();

        if (!array_key_exists($elementKey, $elements)) {
            return $elementKey;
        }

        $count = 0;
        foreach ($elements as $key => $value) {
            if ($elementKey === $key) {
                $count++;
            }
        }

        return $elementKey .= $count;
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    public static function getClassName(AbstractElement $element): string
    {
        $className = get_class($element);
        if ($pos = strrpos($className, '\\')) {
            return substr($className, $pos + 1);
        }

        return '';
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    public static function getType(AbstractElement $element): string
    {
        if ($element instanceof Document) {
            return SafeDeleteConstants::TYPE_DOCUMENT;
        }

        if ($element instanceof Asset) {
            return SafeDeleteConstants::TYPE_ASSET;
        }

        if ($element instanceof DataObject) {
            return SafeDeleteConstants::TYPE_OBJECT;
        }
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    public static function getIcon(AbstractElement $element): string
    {
        if ($element instanceof Document) {
            return ElementDependencyProviderUtils::getDocumentIcon($element);
        }

        if ($element instanceof Asset) {
            return ElementDependencyProviderUtils::getAssetIcon($element);
        }

        if ($element instanceof DataObject) {
            return ElementDependencyProviderUtils::getObjectIcon($element);
        }
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    public static function getAssetIcon(AbstractElement $element): string
    {
        $icon = SafeDeleteConstants::PIMCORE_DEFAULT_ASSET_ICON;

        $fileExt = pathinfo($element->getFilename(), PATHINFO_EXTENSION);
        if ($fileExt) {
            $icon .= ' ' . SafeDeleteConstants::PIMCORE_ICON_PREFIX . strtolower(
                    pathinfo($element->getFilename(), PATHINFO_EXTENSION)
                );
        }

        return $icon;
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    public static function getDocumentIcon(AbstractElement $element): string
    {
        $icon = SafeDeleteConstants::PIMCORE_ICON_PREFIX . strtolower($element->getType());

        return $icon;
    }

    /**
     * @param AbstractElement $element
     * @return string
     * @throws \Exception
     */
    public static function getObjectIcon(AbstractElement $element): string
    {
        $classDefinition = ClassDefinition::getById($element->getClassId());

        $icon = $classDefinition->getIcon();
        $icon = $icon != null ? $icon : SafeDeleteConstants::DEFAULT_ICON_PATH;

        return $icon;
    }
}
