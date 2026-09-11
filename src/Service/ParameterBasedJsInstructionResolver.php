<?php

declare(strict_types=1);

namespace Factotum\SafeDeleteBundle\Service;

use Factotum\SafeDeleteBundle\DTO\JsInstruction\ParameterBasedJsInstructionDtoFactory;
use Factotum\SafeDeleteBundle\DTO\JsInstruction\ParameterBasedJsInstructionRequest;
use Factotum\SafeDeleteBundle\DTO\JsInstruction\ParameterBasedJsInstructionResponse;
use Factotum\SafeDeleteBundle\Exception\InvalidParameterException;
use Pimcore\Model\DataObject;
use Symfony\Contracts\Translation\TranslatorInterface;

class ParameterBasedJsInstructionResolver
{
    private const INVALID_ID_ERROR_MESSAGE_KEY = 'invalid_id_error_message';
    private const ELEMENT_NOT_FOUND_MESSAGE_KEY = 'element_not_found_error_message';
    private const INVALID_TYPE_ERROR_MESSAGE_KEY = 'invalid_type_error_message';
    private const ADMIN_DOMAIN = 'admin';
    private const SUCCESS_INSTRUCTION = "pimcore.treenodelocator.showInTree(%s, '%s', false); pimcore.helpers.openElement(%s, '%s')";
    private const ERROR_INSTRUCTION = "Ext.MessageBox.alert('Error', '%s')";

    /**
     * @param ParameterBasedJsInstructionDtoFactory $parameterBasedJsInstructionDtoFactory
     * @param TranslatorInterface $translator
     */
    public function __construct(
        private readonly ParameterBasedJsInstructionDtoFactory $parameterBasedJsInstructionDtoFactory,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * @param ParameterBasedJsInstructionRequest $request
     * @return ParameterBasedJsInstructionResponse
     */
    public function resolve(
        ParameterBasedJsInstructionRequest $request
    ): ParameterBasedJsInstructionResponse {
        $id = $request->getId();
        $type = $request->getType();

        try {
            $this->validate($id, $type);

            return $this->parameterBasedJsInstructionDtoFactory->createResponse(
                $this->buildSuccessExecString($id, $type),
                true
            );
        } catch (InvalidParameterException $exception) {
            return $this->parameterBasedJsInstructionDtoFactory->createResponse(
                $this->buildFailureExecString($exception->getMessage()),
                false
            );
        }
    }

    /**
     * @param string $id
     * @param string $type
     * @return void
     * @throws InvalidParameterException
     */
    private function validate(string $id, string $type): void
    {
        $this->validateId($id);
        $this->validateType($type);
    }

    /**
     * @param string $id
     * @return void
     * @throws InvalidParameterException
     */
    private function validateId(string $id): void
    {
        if (!ctype_digit($id)) {
            throw new InvalidParameterException(
                $this->translateError(self::INVALID_ID_ERROR_MESSAGE_KEY, $id)
            );
        }

        if (!DataObject::getById($id)) {
            throw new InvalidParameterException(
                $this->translateError(self::ELEMENT_NOT_FOUND_MESSAGE_KEY, $id)
            );
        }
    }

    /**
     * @param string $type
     * @return void
     * @throws InvalidParameterException
     */
    private function validateType(string $type): void
    {
        if (!in_array($type, DataObject::getTypes(), true)) {
            throw new InvalidParameterException(
                $this->translateError(self::INVALID_TYPE_ERROR_MESSAGE_KEY, $type)
            );
        }
    }

    /**
     * @param string $messageKey
     * @param string $value
     * @return string
     */
    private function translateError(string $messageKey, string $value): string
    {
        return sprintf(
            $this->translator->trans($messageKey, [], self::ADMIN_DOMAIN),
            $value
        );
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
     * @param string $id
     * @param string $type
     * @return string
     */
    private function buildSuccessExecString(string $id, string $type): string
    {
        return sprintf(
            self::SUCCESS_INSTRUCTION,
            $id,
            $type,
            $id,
            $type
        );
    }
}
