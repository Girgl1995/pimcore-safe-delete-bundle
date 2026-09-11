<?php

declare(strict_types=1);

namespace Factotum\SafeDeleteBundle\Service;

use Factotum\SafeDeleteBundle\Service\Listing\Factory\ListingProviderFactory;
use Factotum\SafeDeleteBundle\Service\Listing\Interface\ListingProvider;
use Pimcore\Model\DataObject\Listing;
use Pimcore\Model\DataObject\Folder;

class ElementFinder
{
    private const CONDITION_IDS = '`id` IN (?)';
    private const CONDITION_PATHS = '`path` IN (?)';
    private const TYPE_FOLDER = 'folder';
    private const ID_PREFIX = 'id_';
    private const ROOT_DIRECTORY = '/';

    /**
     * @param array $elementDataList
     * @param bool $includeChildren
     * @return array
     */
    public function findElements(array $elementDataList, bool $includeChildren): array
    {
        $elements = $this->getSelectedElements($elementDataList);

        if (!$includeChildren) {
            return $this->excludeFolders($elements);
        }

        return $this->getAllDescendantElements($elements);
    }

    /**
     * @param array $elementDataList
     * @return array
     */
    private function getSelectedElements(array $elementDataList): array
    {
        $elements = $this->getElementsByIds($this->getIds($elementDataList));

        $selectedElements = [];
        foreach ($elements as $element) {
            if ($element->getFullPath() !== self::ROOT_DIRECTORY) {
                $selectedElements[self::ID_PREFIX . strval($element->getId())] = $element;
            }
        }

        return $selectedElements;
    }

    /**
     * @param array $elements
     * @return array
     */
    private function getAllDescendantElements(array $elements): array
    {
        $result = [];

        $children = $this->getChildren($elements);

        if (!$children) {
            return $this->excludeFolders($elements);
        }

        while ($children) {
            foreach ($children as $child) {
                $result[self::ID_PREFIX . strval($child->getId())] = $child;
            }

            $children = $this->getChildren($children);
        }

        return $this->excludeFolders(array_merge($elements, $result));
    }

    /**
     * @param array $elements
     * @return array
     */
    private function getChildren(array $elements): array
    {
        $paths = [];

        foreach ($elements as $element) {
            $children = $element->getChildren()->getData();
            foreach ($children as $child) {
                $paths[] = $child->getPath();
            }
        }

        return $paths
            ? $this->getElementsByPaths($paths)
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
            if (!$element instanceof Folder) {
                $result[] = $element;
            }
        }

        return $result;
    }

    /**
     * @param mixed $ids
     * @return array
     */
    private function getElementsByIds(array $ids): array
    {
        $listing = new Listing();
        $listing->setCondition(self::CONDITION_IDS, [$ids]);

        return $listing->getData();
    }

    /**
     * @param array $elementDataList
     * @return array
     */
    private function getIds(array $elementDataList): array
    {
        $ids = [];

        foreach ($elementDataList as $elementData) {
            $ids[] = $elementData->getId();
        }

        return $ids;
    }

    /**
     * @param array $paths
     * @return array
     */
    private function getElementsByPaths(array $paths): array
    {
        $listing = new Listing();
        $listing->setCondition(self::CONDITION_PATHS, [$paths]);

        return $listing->getData();
    }
}
