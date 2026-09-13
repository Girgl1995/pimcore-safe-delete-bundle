<?php

declare(strict_types=1);

namespace Factotum\SafeDeleteBundle\Service;

use Factotum\SafeDeleteBundle\DTO\JsInstruction\ParameterBasedJsInstructionDtoFactory;
use Factotum\SafeDeleteBundle\DTO\JsInstruction\ParameterBasedJsInstructionRequest;
use Factotum\SafeDeleteBundle\DTO\JsInstruction\ParameterBasedJsInstructionResponse;
use Factotum\SafeDeleteBundle\Exception\InvalidParameterException;
use Factotum\SafeDeleteBundle\SafeDeleteConstants;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;
use Pimcore\Model\Document;
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
        try {
            $this->validate($request);

            return $this->parameterBasedJsInstructionDtoFactory->createResponse(
                $this->buildSuccessExecString($request),
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
     * @param ParameterBasedJsInstructionRequest $request
     * @return void
     * @throws InvalidParameterException
     */
    private function validate(ParameterBasedJsInstructionRequest $request): void
    {
        $this->validateType($request);
        $this->validateId($request);
        $this->validateElementExistence($request);
    }

    /**
     * @param ParameterBasedJsInstructionRequest $request
     * @return void
     */
    private function validateElementExistence(ParameterBasedJsInstructionRequest $request): void
    {
        $id = $request->getId();
        $type = $request->getType();

        if ($type === SafeDeleteConstants::TYPE_OBJECT) {
            if (!DataObject::getById($id)) {
                throw new InvalidParameterException(
                    $this->translateError(self::ELEMENT_NOT_FOUND_MESSAGE_KEY, $id)
                );
            }
        }

        if ($type === SafeDeleteConstants::TYPE_DOCUMENT) {
            if (!Document::getById($id)) {
                throw new InvalidParameterException(
                    $this->translateError(self::ELEMENT_NOT_FOUND_MESSAGE_KEY, $id)
                );
            }
        }

        if ($type === SafeDeleteConstants::TYPE_ASSET) {
            if (!Asset::getById($id)) {
                throw new InvalidParameterException(
                    $this->translateError(self::ELEMENT_NOT_FOUND_MESSAGE_KEY, $id)
                );
            }
        }
    }

    /**
     * @param ParameterBasedJsInstructionRequest $request
     * @return void
     * @throws InvalidParameterException
     */
    private function validateId(ParameterBasedJsInstructionRequest $request): void
    {
        $id = $request->getId();

        if (!ctype_digit($id)) {
            throw new InvalidParameterException(
                $this->translateError(self::INVALID_ID_ERROR_MESSAGE_KEY, $id)
            );
        }
    }

    /**
     * @param ParameterBasedJsInstructionRequest $request
     * @return void
     * @throws InvalidParameterException
     */
    private function validateType(ParameterBasedJsInstructionRequest $request): void
    {
        $type = $request->getType();

        if (!in_array(
            $type,
            [SafeDeleteConstants::TYPE_DOCUMENT, SafeDeleteConstants::TYPE_ASSET, SafeDeleteConstants::TYPE_OBJECT],
            true
        )) {
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
     * @param ParameterBasedJsInstructionRequest $request
     * @return string
     */
    private function buildSuccessExecString(ParameterBasedJsInstructionRequest $request): string
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
