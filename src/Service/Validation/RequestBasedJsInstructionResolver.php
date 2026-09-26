<?php

declare(strict_types=1);

namespace Factotum\SafeDeleteBundle\Service\Validation;

use Factotum\SafeDeleteBundle\DTO\Validation\ValidationDtoFactory;
use Factotum\SafeDeleteBundle\DTO\Validation\ValidationRequestDto;
use Factotum\SafeDeleteBundle\DTO\Validation\ValidationResponseDto;
use Factotum\SafeDeleteBundle\Exception\InvalidParameterException;

class RequestBasedJsInstructionResolver
{
    private const SUCCESS_INSTRUCTION = "pimcore.treenodelocator.showInTree(%s, '%s', false); pimcore.helpers.openElement(%s, '%s')";
    private const ERROR_INSTRUCTION = "Ext.MessageBox.alert('Error', '%s')";

    /**
     * @param ValidationDtoFactory $validationDtoFactory
     * @param RequestValidator $requestValidator
     */
    public function __construct(
        private readonly ValidationDtoFactory $validationDtoFactory,
        private readonly RequestValidator $requestValidator,
    ) {
    }

    /**
     * @param ValidationRequestDto $request
     * @return ValidationResponseDto
     */
    public function resolve(
        ValidationRequestDto $request
    ): ValidationResponseDto {
        try {
            $this->requestValidator->validate($request);

            return $this->validationDtoFactory->createResponse(
                $this->buildSuccessExecString($request),
                true
            );
        } catch (InvalidParameterException $exception) {
            return $this->validationDtoFactory->createResponse(
                $this->buildFailureExecString($exception->getMessage()),
                false
            );
        }
    }

    /**
     * @param string $errorMessage
     * @return string
     */
    private function buildFailureExecString(string $errorMessage): string
    {
        return sprintf(
            self::ERROR_INSTRUCTION,
            $errorMessage
        );
    }

    /**
     * @param ValidationRequestDto $request
     * @return string
     */
    private function buildSuccessExecString(ValidationRequestDto $request): string
    {
        $id = $request->getId();
        $type = $request->getType();

        return sprintf(
            self::SUCCESS_INSTRUCTION,
            $id,
            $type,
            $id,
            $type
        );
    }
}
