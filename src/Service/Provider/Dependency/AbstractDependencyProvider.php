<?php

declare(strict_types=1);

namespace Factotum\SafeDeleteBundle\Service\Provider\Dependency;

use Factotum\SafeDeleteBundle\SafeDeleteConstants;
use Factotum\SafeDeleteBundle\Service\Provider\Dependency\Interface\DependencyProvider;
use Factotum\SafeDeleteBundle\Service\Provider\Dependency\Utils\DependencyProviderUtils;
use Pimcore\Model\DataObject;
use Pimcore\Model\Document;
use Pimcore\Model\Asset;
use Pimcore\Model\Element\AbstractElement;

abstract class AbstractDependencyProvider implements DependencyProvider
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
     * @param AbstractElement $element
     * @return array
     */
    public function getElementDependencies(AbstractElement $element): array
    {
        $result = [];

        $dependencies = $element->getDependencies()->getRequiredBy();
        foreach ($dependencies as $dependency) {
            if ($dependency[self::TYPE_KEY] === SafeDeleteConstants::TYPE_DOCUMENT) {
                $element = Document::getById($dependency[self::ID_KEY]);
                $element->setType(SafeDeleteConstants::TYPE_DOCUMENT);
            }

            if ($dependency[self::TYPE_KEY] === SafeDeleteConstants::TYPE_ASSET) {
                $element = Asset::getById($dependency[self::ID_KEY]);
                $element->setType(SafeDeleteConstants::TYPE_ASSET);
            }

            if ($dependency[self::TYPE_KEY] === SafeDeleteConstants::TYPE_OBJECT) {
                $element = DataObject::getById($dependency[self::ID_KEY]);
            }

            if (!$element) {
                continue;
            }

            $result[] = $this->getDependencyData($element);
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
            self::TYPE_KEY => $element->getType(),
            self::PATH_KEY => $element->getPath(),
            self::SUBTYPE_KEY => DependencyProviderUtils::getClassName($element),
        ];
    }
}
