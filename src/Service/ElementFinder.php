<?php

declare(strict_types=1);

namespace Factotum\SafeDeleteBundle\Service;

use Factotum\SafeDeleteBundle\Service\Provider\Element\Interface\ElementProviderInterface;
use Pimcore\Model\Element\AbstractElement;

class ElementFinder
{
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
        $selectedElements = $this->getSelectedElements($elementProvider, $elementDataList);
        if (!$includeChildren) {
            return $selectedElements;
        }

        $descendants = $this->searchDescendants($elementProvider, $selectedElements);
        if (!$descendants) {
            return $selectedElements;
        }

        return array_merge($selectedElements, $descendants);
    }

    /**
     * @param ElementProviderInterface $elementProvider
     * @param array $elementDataList
     * @return array
     */
    private function getSelectedElements(ElementProviderInterface $elementProvider, array $elementDataList): array
    {
        $elements = $elementProvider->getElementsByIds($this->getElementIds($elementDataList));

        return $this->reindexById($elements);
    }

    /**
     * @param ElementProviderInterface $elementProvider
     * @param array $elements
     * @return array
     */
    private function searchDescendants(ElementProviderInterface $elementProvider, array $elements): array
    {
        $paths = [];

        foreach ($elements as $element) {
            $children = $element->getChildren()->getData();
            if ($children) {
                $paths[] = $this->buildPath($element);
            }
        }

        if (!$paths) {
            return [];
        }

        return $this->reindexById($elementProvider->getElementsByPaths($paths));
    }

    /**
     * @param array $elements
     * @return array
     */
    private function reindexById(array $elements): array
    {
        $result = [];

        foreach ($elements as $element) {
            if ($element->getFullPath() !== self::ROOT_DIRECTORY) {
                $result[self::ID_PREFIX . strval($element->getId())] = $element;
            }
        }

        return $result;
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    private function buildPath(AbstractElement $element): string
    {
        return $element->getPath() . $element->getKey();
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
