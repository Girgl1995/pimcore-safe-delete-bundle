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
    private const RESPONSE_KEY_RESULT = 'result';
    private const RESPOSNE_KEY_SUCCESS = 'success';
    private const REQUEST_KEY_ID = 'id';
    private const REQUEST_KEY_TYPE = 'type';

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
        $id = $request->query->get(self::REQUEST_KEY_ID);
        $type = $request->query->get(self::REQUEST_KEY_TYPE);

        $request = $parameterBasedJsInstructionDtoFactory->createRequest($id, $type);

        $response = $parameterBasedJsInstructionResolver->resolve($request);

        return new JsonResponse(
            [self::RESPONSE_KEY_RESULT => $response->getAction(), self::RESPOSNE_KEY_SUCCESS => $response->getSuccess()]
        );
    }
}
