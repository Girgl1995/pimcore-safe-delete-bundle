<?php

declare(strict_types=1);

namespace Factotum\SafeDeleteBundle\DTO\Validation;

readonly class ValidationResponseDto
{
    /**
     * @param string $action
     * @param bool $success
     */
    public function __construct(
        private string $action,
        private bool $success,
    ) {
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @return bool
     */
    public function getSuccess(): bool
    {
        return $this->success;
    }
}
