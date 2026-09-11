<?php

declare(strict_types=1);

namespace Factotum\SafeDeleteBundle\DTO\JsInstruction;

readonly class ParameterBasedJsInstructionRequest
{
    /**
     * @param string $id
     * @param string $type
     */
    public function __construct(
        private string $id,
        private string $type,
    ) {
    }

    /**
     * @return string
     */
    public function getId(): string
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
}
