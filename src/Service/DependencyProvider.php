<?php

declare(strict_types=1);

namespace Factotum\SafeDeleteBundle\Service;

use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition;

class DependencyProvider
{
    private const ID_KEY = 'id';
    private const NAME_KEY = 'name';
    private const TYPE_KEY = 'type';
    private const PATH_KEY = 'path';
    private const SUBTYPE_KEY = 'subtype';
    private const DATA_KEY = 'data';
    private const ICON_KEY = 'icon';
    private const KEY_KEY = 'key';
    private const DEFAULT_ICON_PATH = '/bundles/pimcoreadmin/img/flat-color-icons/object.svg';

    /**
     * @param array|null $objects
     * @return array
     * @throws \Exception
     */
    public function getDependencies(?array $objects): array
    {
        $result = [];
        foreach ($objects as $object) {
            $dependencies = $this->getObjectDependencies($object);
            if (!$dependencies) {
                continue;
            }

            $key = $this->generateKey($object, $result);

            $classDefinition = ClassDefinition::getById($object->getClassId());

            $icon = $classDefinition->getIcon();
            $icon = $icon != null ? $icon : self::DEFAULT_ICON_PATH;

            $result[$key] = [
                self::DATA_KEY => $dependencies,
                self::ID_KEY => $object->getId(),
                self::KEY_KEY => $object->getKey(),
                self::TYPE_KEY => $object->getType(),
                self::PATH_KEY => $object->getPath(),
                self::ICON_KEY => $icon,
            ];
        }

        return $result;
    }

    /**
     * @param DataObject $object
     * @return array
     */
    private function getObjectDependencies(DataObject $object): array
    {
        $result = [];

        $dependencies = $object->getDependencies()->getRequiredBy();
        foreach ($dependencies as $dependency) {
            $object = DataObject::getById($dependency[self::ID_KEY]);

            if (!$object) {
                continue;
            }

            $result[] = $this->getDependencyData($object);
        }

        return $result;
    }

    /**
     * @param DataObject $object
     * @return array
     */
    private function getDependencyData(DataObject $object): array
    {
        return [
            self::ID_KEY => $object->getId(),
            self::NAME_KEY => $object->getKey(),
            self::TYPE_KEY => $object->getType(),
            self::PATH_KEY => $object->getPath(),
            self::SUBTYPE_KEY => $object->getClassName(),
        ];
    }

    /**
     * @param DataObject $object
     * @param array $result
     * @return string
     */
    private function generateKey(DataObject $object, array $result): string
    {
        $objectKey = $object->getKey();

        if (!array_key_exists($objectKey, $result)) {
            return $objectKey;
        }

        $count = 0;
        foreach($result as $key => $value) {
            if ($objectKey === $key) {
                $count++;
            }
        }

        return $key .= $count;
    }
}
