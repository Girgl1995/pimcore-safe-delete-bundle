<?php

declare(strict_types=1);

namespace Factotum\SafeDeleteBundle\Service\Validation;

use Factotum\SafeDeleteBundle\DTO\Validation\ValidationRequestDto;
use Factotum\SafeDeleteBundle\Exception\InvalidParameterException;
use Factotum\SafeDeleteBundle\SafeDeleteConstants;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;
use Pimcore\Model\Document;
use Symfony\Contracts\Translation\TranslatorInterface;

class RequestValidator
{
    private const INVALID_ID_ERROR_MESSAGE_KEY = 'invalid_id_error_message';
    private const ELEMENT_NOT_FOUND_MESSAGE_KEY = 'element_not_found_error_message';
    private const INVALID_TYPE_ERROR_MESSAGE_KEY = 'invalid_type_error_message';
    private const ADMIN_DOMAIN = 'admin';

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * @param ValidationRequestDto $request
     * @return void
     * @throws InvalidParameterException
     */
    public function validate(
        ValidationRequestDto $request
    ): void {
        try {
            $this->validateType($request);
            $this->validateId($request);
            $this->validateElementExistence($request);
        } catch (InvalidParameterException $e) {
            throw $e;
        }
    }

    /**
     * @param ValidationRequestDto $request
     * @return void
     * @throws InvalidParameterException
     */
    private function validateType(ValidationRequestDto $request): void
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
     * @param ValidationRequestDto $request
     * @return void
     * @throws InvalidParameterException
     */
    private function validateId(ValidationRequestDto $request): void
    {
        $id = $request->getId();

        if (!ctype_digit($id)) {
            throw new InvalidParameterException(
                $this->translateError(self::INVALID_ID_ERROR_MESSAGE_KEY, $id)
            );
        }
    }

    /**
     * @param ValidationRequestDto $request
     * @return void
     */
    private function validateElementExistence(ValidationRequestDto $request): void
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
}
