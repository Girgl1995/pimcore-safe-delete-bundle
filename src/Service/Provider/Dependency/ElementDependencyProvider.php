<?php

declare(strict_types=1);

namespace Factotum\SafeDeleteBundle\Service\Provider\Dependency;

use Factotum\SafeDeleteBundle\SafeDeleteConstants;
use Factotum\SafeDeleteBundle\Service\Provider\Dependency\Utils\ElementDependencyProviderUtils;
use Pimcore\Model\DataObject;
use Pimcore\Model\Document;
use Pimcore\Model\Asset;
use Pimcore\Model\Element\AbstractElement;


class ElementDependencyProvider
{
    private const ID_KEY = 'id';
    private const NAME_KEY = 'name';
    private const TYPE_KEY = 'type';
    private const PATH_KEY = 'path';
    private const SUBTYPE_KEY = 'subtype';
    private const DATA_KEY = 'data';
    private const ICON_KEY = 'icon';
    private const KEY_KEY = 'key';

    /**
     * @param array|null $elements
     * @return array
     */
    public function getDependencies(?array $elements): array
    {
        $result = [];
        foreach ($elements as $element) {
            $key = ElementDependencyProviderUtils::generateUniqueArrayKeyForElement($result, $element);

            $dependencies = $this->getDependenciesOf($element);
            if (!$dependencies) {
                continue;
            }

            $result[$key] = [
                self::DATA_KEY => $dependencies,
                self::ID_KEY => $element->getId(),
                self::KEY_KEY => $element->getKey(),
                self::TYPE_KEY => ElementDependencyProviderUtils::getType($element),
                self::PATH_KEY => $element->getPath(),
                self::ICON_KEY => ElementDependencyProviderUtils::getIcon($element),
            ];
        }

        return $result;
    }

    /**
     * @param AbstractElement $element
     * @return array
     */
    private function getDependenciesOf(AbstractElement $element): array
    {
        $result = [];

        $dependencies = $element->getDependencies()->getRequiredBy();
        foreach ($dependencies as $dependency) {
            $type = $dependency[self::TYPE_KEY];

            $concrete = $this->getConcreteElement($dependency, $type);
            if (!$concrete) {
                continue;
            }

            $data = $this->buildDependencyData($concrete);
            if (!$data) {
                continue;
            }

            $result[] = $data;
        }

        return $result;
    }

    /**
     * @param array $element
     * @param string $type
     * @return AbstractElement|null
     */
    private function getConcreteElement(array $element, string $type): ?AbstractElement
    {
        if ($type === SafeDeleteConstants::TYPE_ASSET) {
            return Asset::getById($element[self::ID_KEY]);
        }

        if ($type === SafeDeleteConstants::TYPE_DOCUMENT) {
            return Document::getById($element[self::ID_KEY]);
        }

        if ($type === SafeDeleteConstants::TYPE_OBJECT) {
            return DataObject::getById($element[self::ID_KEY]);
        }
    }

    /**
     * @param AbstractElement $element
     * @return array
     */
    private function buildDependencyData(AbstractElement $element): array
    {
        return [
            self::ID_KEY => $element->getId(),
            self::NAME_KEY => $element->getKey(),
            self::PATH_KEY => $element->getPath(),
            self::TYPE_KEY => ElementDependencyProviderUtils::getType($element),
            self::SUBTYPE_KEY => ElementDependencyProviderUtils::getClassName($element),
        ];
    }
}
