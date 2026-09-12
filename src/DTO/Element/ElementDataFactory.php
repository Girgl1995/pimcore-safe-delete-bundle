<?php

declare(strict_types=1);

namespace Factotum\SafeDeleteBundle\DTO\Element;

use Pimcore\Model\Element\AbstractElement;

class ElementDataFactory
{
    private const ID_KEY = 'id';
    private const TYPE_KEY = 'type';
    private const VALUE_KEY = 'key';
    private const PATH_KEY = 'path';

    /**
     * @param array $data
     * @return ElementData
     */
    public function fromArray(array $data): ElementData
    {
        return new ElementData(
            $data[self::ID_KEY], $data[self::TYPE_KEY], $data[self::VALUE_KEY], $data[self::PATH_KEY]
        );
    }

    /**
     * @param AbstractElement $element
     * @return ElementData
     */
    public function fromElement(AbstractElement $element): ElementData
    {
        return new ElementData(
            $element->getId(), $element->getType(), $element->getKey(), $element->getPath()
        );
    }

    /**
     * @param array $data
     * @return array
     */
    public function fromArrayCollection(array $data): array
    {
        return array_map(
            fn(array $item): ElementData => $this->fromArray($item),
            $data
        );
    }

    /**
     * @param array $data
     * @return array
     */
    public function fromElementCollection(array $data): array
    {
        return array_map(
            fn(AbstractElement $item): ElementData => $this->fromElement($item),
            $data
        );
    }
}
