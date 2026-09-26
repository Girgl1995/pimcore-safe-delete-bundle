<?php

declare(strict_types=1);

namespace Factotum\SafeDeleteBundle\Service;

use Factotum\SafeDeleteBundle\Service\Provider\Element\Interface\ElementProviderInterface;

class ElementFinder
{
    private const TYPE_FOLDER    = 'folder';
    private const ID_PREFIX      = 'id_';
    private const ROOT_DIRECTORY = '/';

    /**
     * @param ElementProviderInterface $elementProvider
     * @param array $elementDataList
     * @param boolean $includeChildren
     * @return array
     */
    public function findElements(ElementProviderInterface $elementProvider, array $elementDataList, bool $includeChildren): array
    {
        $elements = $this->getSelectedElements($elementProvider, $elementDataList);

        if (!$includeChildren) {
            return $this->excludeFolders($elements);
        }

        return $this->findDescendants($elementProvider, $elements);
    }

    /**
     * @param ElementProviderInterface $elementProvider
     * @param array $elementDataList
     * @return array
     */
    private function getSelectedElements(ElementProviderInterface $elementProvider, array $elementDataList): array
    {
        $elements = $elementProvider->getElementsByIds($this->getElementIds($elementDataList));

        $selectedElements = [];
        foreach ($elements as $element) {
            if ($element->getFullPath() !== self::ROOT_DIRECTORY) {
                $selectedElements[self::ID_PREFIX . strval($element->getId())] = $element;
            }
        }

        return $selectedElements;
    }

    /**
     * @param ElementProviderInterface $elementProvider
     * @param array $elements
     * @return array
     */
    private function findDescendants(ElementProviderInterface $elementProvider, array $elements): array
    {
        $result = [];

        $children = $this->getChildren($elementProvider, $elements);

        if (!$children) {
            return $this->excludeFolders($elements);
        }

        while ($children) {
            foreach ($children as $child) {
                $result[self::ID_PREFIX . strval($child->getId())] = $child;
            }

            $children = $this->getChildren($elementProvider, $children);
        }

        return $this->excludeFolders(array_merge($elements, $result));
    }

    /**
     * @param ElementProviderInterface $elementProvider
     * @param array $elements
     * @return array
     */
    private function getChildren(ElementProviderInterface $elementProvider, array $elements): array
    {
        $paths = [];

        foreach ($elements as $element) {
            $children = $element->getChildren()->getData();
            foreach ($children as $child) {
                $paths[] = $child->getPath();
            }
        }

        return $paths
            ? $elementProvider->getElementsByPaths($paths)
            : [];
    }

    /**
     * @param array $entries
     * @return array
     */
    private function excludeFolders(array $entries): array
    {
        $result = [];

        foreach ($entries as $element) {
            if ($element->getType() !== self::TYPE_FOLDER) {
                $result[] = $element;
            }
        }

        return $result;
    }

    /**
     * @param array $elementDataList
     * @return array
     */
    private function getElementIds(array $elementDataList): array
    {
        $ids = [];

        foreach ($elementDataList as $elementData) {
            $ids[] = $elementData->getId();
        }

        return $ids;
    }
}
