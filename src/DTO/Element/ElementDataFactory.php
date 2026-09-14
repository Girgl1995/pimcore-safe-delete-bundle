<?php

declare(strict_types=1);

namespace Factotum\SafeDeleteBundle\DTO\Element;

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
}
