<?php

declare(strict_types=1);

namespace Factotum\SafeDeleteBundle\Controller;

use Factotum\SafeDeleteBundle\DTO\Validation\ValidationDtoFactory;
use Factotum\SafeDeleteBundle\Service\Validation\RequestBasedJsInstructionResolver;
use Factotum\SafeDeleteBundle\Service\Validation\RequestValidator;
use Pimcore\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ParameterValueValidationController extends Controller
{
    private const RESPONSE_RESULT_KEY = 'result';
    private const RESPONSE_SUCCESS_KEY = 'success';
    private const REQUEST_ID_KEY = 'id';
    private const REQUEST_TYPE_KEY = 'type';

    /**
     * @param Request $request
     * @param ValidationDtoFactory $validationDtoFactory
     * @param RequestBasedJsInstructionResolver $requestBasedJsInstructionResolver
     * @return JsonResponse
     */
    #[Route('/admin/parametervaluevalidation/validate-parameter-values', name: 'validate_parameter_values', options: ['expose' => true], methods: ['GET'])]
    public function validateParameterValues(
        Request $request,
        ValidationDtoFactory $validationDtoFactory,
        RequestValidator $requestValidator,
        RequestBasedJsInstructionResolver $requestBasedJsInstructionResolver
    ): JsonResponse {
        $id = (string)$request->query->get(self::REQUEST_ID_KEY);
        $type = (string)$request->query->get(self::REQUEST_TYPE_KEY);

        $request = $validationDtoFactory->createRequest($id, $type);

        $response = $requestBasedJsInstructionResolver->resolve($request);

        return new JsonResponse(
            [self::RESPONSE_RESULT_KEY => $response->getAction(), self::RESPONSE_SUCCESS_KEY => $response->getSuccess()]
        );
    }
}
