<?php

declare(strict_types=1);

namespace Factotum\SafeDeleteBundle\Controller;

use Exception;
use Factotum\SafeDeleteBundle\Config\SafeDeleteConfig;
use Factotum\SafeDeleteBundle\DTO\Element\ElementDataFactory;
use Factotum\SafeDeleteBundle\Service\DependencyProvider;
use Factotum\SafeDeleteBundle\Service\ElementFinder;
use Pimcore\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class SafeDeleteController extends Controller
{
    private const REQUEST_SELECTED_ITEMS_KEY = 'selected_items';
    private const INCLUDE_CHILDREN_KEY = 'include_children';
    private const RESPONSE_KEY_RESULT = 'result';

    /**
     * @param Request $request
     * @param ElementFinder $elementFinder
     * @param DependencyProvider $dependencyProvider
     * @param ElementDataFactory $elementDataFactory
     * @return JsonResponse
     * @throws Exception
     */
    #[Route('/admin/safedelete/get-element-dependencies', name: 'get_element_dependencies', options: ['expose' => true], methods: ['GET'])]
    public function getDependenciesAction(
        Request $request,
        ElementFinder $elementFinder,
        DependencyProvider $dependencyProvider,
        ElementDataFactory $elementDataFactory,
    ): JsonResponse {
        $selectedItems = json_decode($request->query->get(self::REQUEST_SELECTED_ITEMS_KEY), true);
        $includeChildren = json_decode($request->query->get(self::INCLUDE_CHILDREN_KEY), true);

        $elementDataList = $elementDataFactory->fromArrayCollection($selectedItems);

        $elements = $elementFinder->findElements($elementDataList, $includeChildren);

        $result = $dependencyProvider->getDependencies($elements);

        return new JsonResponse([self::RESPONSE_KEY_RESULT => json_encode($result)]);
    }

    /**
     * @param Request $request
     * @param SafeDeleteConfig $config
     * @return JsonResponse
     */
    #[Route('/admin/safedelete/check-config', name: 'check_config', options: ['expose' => true], methods: ['GET'])]
    public function checkConfigAction(Request $request, SafeDeleteConfig $config): JsonResponse
    {
        return new JsonResponse(
            [
                self::INCLUDE_CHILDREN_KEY => $config->getincludeChildren(),
            ]
        );
    }
}
