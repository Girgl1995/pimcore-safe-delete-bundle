<?php

declare(strict_types=1);

namespace Factotum\SafeDeleteBundle\DTO\Element;

class ElementDataFactory
{
    private const KEY_ID = 'id';
    private const KEY_TYPE = 'type';
    private const KEY_VALUE = 'key';
    private const KEY_PATH = 'path';

    /**
     * @param array $data
     * @return ElementData
     */
    public function fromArray(array $data): ElementData
    {
        return new ElementData(
            $data[self::KEY_ID], $data[self::KEY_TYPE], $data[self::KEY_VALUE], $data[self::KEY_PATH]
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
