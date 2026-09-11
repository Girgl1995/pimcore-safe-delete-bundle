<?php

declare(strict_types=1);

namespace Factotum\SafeDeleteBundle\DTO\JsInstruction;

class ParameterBasedJsInstructionDtoFactory
{
    /**
     * @param string $action
     * @param bool $success
     * @return ParameterBasedJsInstructionResponse
     */
    public function createResponse(string $action, bool $success): ParameterBasedJsInstructionResponse
    {
        return new ParameterBasedJsInstructionResponse($action, $success);
    }

    /**
     * @param string $id
     * @param string $type
     * @return ParameterBasedJsInstructionRequest
     */
    public function createRequest(string $id, string $type): ParameterBasedJsInstructionRequest
    {
        return new ParameterBasedJsInstructionRequest($id, $type);
    }
}
