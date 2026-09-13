<?php

declare(strict_types=1);

namespace Factotum\SafeDeleteBundle\Service\Provider\Dependency;

use Factotum\SafeDeleteBundle\Service\Provider\Dependency\Utils\DependencyProviderUtils;
use Pimcore\Model\DataObject;
use Pimcore\Model\Document;
use Pimcore\Model\Asset;
use Pimcore\Model\Element\AbstractElement;
use Factotum\SafeDeleteBundle\Service\Provider\Interface\ProviderInterface;

class DependencyProvider
{
    protected const ID_KEY = 'id';
    protected const NAME_KEY = 'name';
    protected const TYPE_KEY = 'type';
    protected const PATH_KEY = 'path';
    protected const SUBTYPE_KEY = 'subtype';
    protected const DATA_KEY = 'data';
    protected const ICON_KEY = 'icon';
    protected const KEY_KEY = 'key';

    /**
     * @param array|null $elements
     * @return array
     */
    public function getDependencies(?array $elements): array
    {
        $result = [];
        foreach ($elements as $element) {
            $key = DependencyProviderUtils::generateKey($element, $result);

            $data = $this->prepareElementData($element);

            if (!$data) {
                continue;
            }

            $result[$key] = $data;
        }

        return $result;
    }

    /**
     * @param AbstractElement $element
     * @return array
     */
    protected function prepareElementData(AbstractElement $element): array
    {
        $dependencies = $this->getElementDependencies($element);
        if (!$dependencies) {
            return [];
        }

        return [
            self::DATA_KEY => $dependencies,
            self::ID_KEY => $element->getId(),
            self::KEY_KEY => $element->getKey(),
            self::TYPE_KEY => DependencyProviderUtils::getType($element),
            self::PATH_KEY => $element->getPath(),
            self::ICON_KEY => DependencyProviderUtils::getIcon($element),
        ];
    }

    /**
     * @param AbstractElement $element
     * @return array
     */
    public function getElementDependencies(AbstractElement $element): array
    {
        $result = [];

        $dependencies = $element->getDependencies()->getRequiredBy();
        foreach ($dependencies as $dependency) {
            $type = $dependency[self::TYPE_KEY];

            $element = $this->getConcreteElement($dependency, $type);

            $data = $this->getDependencyData($element);

            if (!$data) {
                continue;
            }

            $result[] = $data;
        }

        return $result;
    }

    /**
     * @param AbstractElement $element
     * @return array
     */
    protected function getDependencyData(AbstractElement $element): array
    {
        return [
            self::ID_KEY => $element->getId(),
            self::NAME_KEY => $element->getKey(),
            self::PATH_KEY => $element->getPath(),
            self::TYPE_KEY => DependencyProviderUtils::getType($element),
            self::SUBTYPE_KEY => DependencyProviderUtils::getClassName($element),
        ];
    }

    /**
     * @param array $element
     * @param string $type
     * @return AbstractElement|null
     */
    protected function getConcreteElement(array $element, string $type): ?AbstractElement
    {
        if ($type === ProviderInterface::TYPE_ASSET) {
            return Asset::getById($element[self::ID_KEY]);
        }

        if ($type === ProviderInterface::TYPE_DOCUMENT) {
            return Document::getById($element[self::ID_KEY]);
        }

        if ($type === ProviderInterface::TYPE_OBJECT) {
            return DataObject::getById($element[self::ID_KEY]);
        }
    }
}
