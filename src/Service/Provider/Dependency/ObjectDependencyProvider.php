<?php

declare(strict_types=1);

namespace Factotum\SafeDeleteBundle\Service\Provider\Dependency;

use Factotum\SafeDeleteBundle\Service\Provider\Dependency\Utils\DependencyProviderUtils;
use Pimcore\Model\DataObject\ClassDefinition;
use Factotum\SafeDeleteBundle\SafeDeleteConstants;

class ObjectDependencyProvider extends AbstractDependencyProvider
{
    private const DEFAULT_ICON_PATH = '/bundles/pimcoreadmin/img/flat-color-icons/object.svg';


    /**
     * @param array|null $elements
     * @return array
     * @throws \Exception
     */
    public function getDependencies(?array $elements): array
    {
        $result = [];
        foreach ($elements as $element) {
            $dependencies = $this->getElementDependencies($element);
            if (!$dependencies) {
                continue;
            }

            $key = DependencyProviderUtils::generateKey($element, $result);

            $classDefinition = ClassDefinition::getById($element->getClassId());

            $icon = $classDefinition->getIcon();
            $icon = $icon != null ? $icon : self::DEFAULT_ICON_PATH;

            $result[$key] = [
                self::DATA_KEY => $dependencies,
                self::ID_KEY => $element->getId(),
                self::KEY_KEY => $element->getKey(),
                self::TYPE_KEY => $element->getType(),
                self::PATH_KEY => $element->getPath(),
                self::ICON_KEY => $icon,
            ];
        }

        return $result;
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
