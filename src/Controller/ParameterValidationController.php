<?php

declare(strict_types=1);

namespace Factotum\SafeDeleteBundle\Controller;

use Factotum\SafeDeleteBundle\DTO\JsInstruction\ParameterBasedJsInstructionDtoFactory;
use Factotum\SafeDeleteBundle\Service\ParameterBasedJsInstructionResolver;
use Pimcore\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ParameterValidationController extends Controller
{
    private const RESPONSE_RESULT_KEY = 'result';
    private const RESPONSE_SUCCESS_KEY = 'success';
    private const REQUEST_ID_KEY = 'id';
    private const REQUEST_TYPE_KEY = 'type';

    /**
     * @param Request $request
     * @param ParameterBasedJsInstructionResolver $parameterBasedJsInstructionResolver
     * @param ParameterBasedJsInstructionDtoFactory $parameterBasedJsInstructionDtoFactory
     * @return JsonResponse
     */
    #[Route('/admin/parametervalidation/check-parameter', name: 'check_parameter', options: ['expose' => true], methods: ['GET'])]
    public function checkParameter(
        Request $request,
        ParameterBasedJsInstructionResolver $parameterBasedJsInstructionResolver,
        ParameterBasedJsInstructionDtoFactory $parameterBasedJsInstructionDtoFactory
    ): JsonResponse {
        $id = $request->query->get(self::REQUEST_ID_KEY);
        $type = $request->query->get(self::REQUEST_TYPE_KEY);

        $request = $parameterBasedJsInstructionDtoFactory->createRequest($id, $type);

        $response = $parameterBasedJsInstructionResolver->resolve($request);

        return new JsonResponse(
            [self::RESPONSE_RESULT_KEY => $response->getAction(), self::RESPONSE_SUCCESS_KEY => $response->getSuccess()]
        );
    }
}
