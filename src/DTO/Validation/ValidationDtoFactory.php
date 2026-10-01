<?php

declare(strict_types=1);

namespace Factotum\SafeDeleteBundle\DTO\Validation;

class ValidationDtoFactory
{
    /**
     * @param string $action
     * @param bool $success
     * @return ValidationResponseDto
     */
    public function createResponse(string $action, bool $success): ValidationResponseDto
    {
        return new ValidationResponseDto($action, $success);
    }

    /**
     * @param string $id
     * @param string $type
     * @return ValidationRequestDto
     */
    public function createRequest(string $id, string $type): ValidationRequestDto
    {
        return new ValidationRequestDto($id, $type);
    }
}
