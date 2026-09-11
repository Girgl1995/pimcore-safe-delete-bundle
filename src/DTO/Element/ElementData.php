<?php

declare(strict_types=1);

namespace Factotum\SafeDeleteBundle\DTO\Element;

readonly class ElementData
{
    /**
     * @param int $id
     * @param string $type
     * @param string $key
     * @param string $path
     */
    public function __construct(
        private int $id,
        private string $type,
        private string $key,
        private string $path
    ) {
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }
}
