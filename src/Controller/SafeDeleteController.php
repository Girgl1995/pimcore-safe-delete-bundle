<?php

declare(strict_types=1);

namespace Factotum\SafeDeleteBundle\Controller;

use Factotum\SafeDeleteBundle\Config\SafeDeleteConfig;
use Factotum\SafeDeleteBundle\DTO\Element\ElementDataFactory;
use Factotum\SafeDeleteBundle\Service\ElementFinder;
use Factotum\SafeDeleteBundle\Service\Provider\Dependency\Factory\DependencyProviderFactory;
use Factotum\SafeDeleteBundle\Service\Provider\Element\Factory\ElementProviderFactory;
use Pimcore\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class SafeDeleteController extends Controller
{
    private const REQUEST_SELECTED_ITEMS_KEY = 'selected_items';
    private const INCLUDE_CHILDREN_KEY = 'include_children';
    private const ELEMENT_TYPE_KEY = 'element_type';
    private const RESPONSE_RESULT_KEY = 'result';

    /**
     * @param Request $request
     * @param ElementFinder $elementFinder
     * @param ElementDataFactory $elementDataFactory
     * @param ElementProviderFactory $elementProviderFactory
     * @param DependencyProviderFactory $dependencyProviderFactory
     * @return JsonResponse
     */
    #[Route('/admin/safedelete/get-element-dependencies', name: 'get_element_dependencies', options: ['expose' => true], methods: ['GET'])]
    public function getDependenciesAction(
        Request $request,
        ElementFinder $elementFinder,
        ElementDataFactory $elementDataFactory,
        ElementProviderFactory $elementProviderFactory,
        DependencyProviderFactory $dependencyProviderFactory,
    ): JsonResponse {
        $selectedItems = json_decode($request->query->get(self::REQUEST_SELECTED_ITEMS_KEY), true);
        $includeChildren = (bool)$request->query->get(self::INCLUDE_CHILDREN_KEY);
        $elementType = $request->query->get(self::ELEMENT_TYPE_KEY);

        $elementDataList = $elementDataFactory->fromArrayCollection($selectedItems);

        $elementProvider = $elementProviderFactory->getProvider($elementType);
        $elements = $elementFinder->findElements($elementProvider, $elementDataList, $includeChildren);

        $dependencyProvider = $dependencyProviderFactory->getProvider($elementType);
        $result = $dependencyProvider->getDependencies($elements);

        return new JsonResponse([self::RESPONSE_RESULT_KEY => json_encode($result)]);
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
