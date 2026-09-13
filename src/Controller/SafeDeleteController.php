<?php

declare(strict_types=1);

namespace Factotum\SafeDeleteBundle\Controller;

use Factotum\SafeDeleteBundle\Config\SafeDeleteConfig;
use Factotum\SafeDeleteBundle\DTO\Element\ElementDataFactory;
use Factotum\SafeDeleteBundle\Service\ElementFinder;
use Factotum\SafeDeleteBundle\Service\Provider\Dependency\ElementDependencyProvider;
use Factotum\SafeDeleteBundle\Service\Provider\Element\Factory\ElementProviderFactory;
use Pimcore\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class SafeDeleteController extends Controller
{
    private const REQUEST_SELECTED_ITEMS_KEY = 'selected_items';
    private const ELEMENT_TYPE_KEY = 'element_type';
    private const RESPONSE_RESULT_KEY = 'result';

    /**
     * @param Request $request
     * @param SafeDeleteConfig $safeDeleteConfig
     * @param ElementProviderFactory $elementProviderFactory
     * @param ElementDataFactory $elementDataFactory
     * @param ElementFinder $elementFinder
     * @param ElementDependencyProvider $elementDependencyProvider
     * @return JsonResponse
     */
    #[Route('/admin/safedelete/get-element-dependencies', name: 'get_element_dependencies', options: ['expose' => true], methods: ['GET'])]
    public function getDependenciesAction(
        Request $request,
        SafeDeleteConfig $safeDeleteConfig,
        ElementProviderFactory $elementProviderFactory,
        ElementDataFactory $elementDataFactory,
        ElementFinder $elementFinder,
        ElementDependencyProvider $elementDependencyProvider,
    ): JsonResponse {
        $selectedItems = json_decode($request->query->get(self::REQUEST_SELECTED_ITEMS_KEY), true);
        $elementType = $request->query->get(self::ELEMENT_TYPE_KEY);

        $elementProvider = $elementProviderFactory->getProvider($elementType);
        $elementDataList = $elementDataFactory->fromArrayCollection($selectedItems);
        $includeChildren = $safeDeleteConfig->getIncludeChildren();

        $elements = $elementFinder->findElements($elementProvider, $elementDataList, $includeChildren);

        $result = $elementDependencyProvider->getDependencies($elements);

        return new JsonResponse([self::RESPONSE_RESULT_KEY => json_encode($result)]);
    }
}
