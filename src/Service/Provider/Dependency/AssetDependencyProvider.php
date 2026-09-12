<?php

declare(strict_types=1);

namespace Factotum\SafeDeleteBundle\Service\Provider\Dependency;

use Factotum\SafeDeleteBundle\Service\Provider\Dependency\Utils\DependencyProviderUtils;
use Factotum\SafeDeleteBundle\SafeDeleteConstants;

class AssetDependencyProvider extends AbstractDependencyProvider
{
    /**
     * @param array|null $elements
     * @return array
     */
    public function getDependencies(?array $elements): array
    {
        $result = [];
        foreach ($elements as $element) {
            $dependencies = $this->getElementDependencies($element);
            if (!$dependencies) {
                continue;
            }

            $key = DependencyProviderUtils::generateKey($element, $result);

            $icon = SafeDeleteConstants::PIMCORE_DEFAULT_ASSET_ICON;

            $fileExt = pathinfo($element->getFilename(), PATHINFO_EXTENSION);
            if ($fileExt) {
                $icon .= ' ' . SafeDeleteConstants::PIMCORE_ICON_PREFIX . strtolower(
                        pathinfo($element->getFilename(), PATHINFO_EXTENSION)
                    );
            }

            $result[$key] = [
                self::DATA_KEY => $dependencies,
                self::ID_KEY => $element->getId(),
                self::KEY_KEY => $element->getKey(),
                self::TYPE_KEY => SafeDeleteConstants::TYPE_ASSET,
                self::PATH_KEY => $element->getPath(),
                self::ICON_KEY => $icon,
            ];
        }

        return $result;
    }

    /**
     * @param string $elementType
     * @return bool
     */
    public function supports(string $elementType): bool
    {
        return $elementType === SafeDeleteConstants::TYPE_ASSET;
    }
}
