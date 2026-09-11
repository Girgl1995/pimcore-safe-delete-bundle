<?php

declare(strict_types=1);

namespace Factotum\SafeDeleteBundle\DependencyInjection;

use Exception;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension as SymfonyExtension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

class Extension extends SymfonyExtension
{
    public const BUNDLE_ALIAS = 'factotum_safe_delete';
    private const CONFIG_DIR = '/../../config';
    private const CONFIG_FILE = 'services.yaml';
    private const SAFE_DELETE_CONFIG_NAMESPACE = 'Factotum\SafeDeleteBundle\Config\SafeDeleteConfig';
    private const CONFIG_ATTRIBUTE = '$config';
    private const CONFIG_INCLUDE_CHILDREN_KEY = 'include_children';

    /**
     * @param array $configs
     * @param ContainerBuilder $container
     * @return void
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . self::CONFIG_DIR)
        );

        $loader->load(self::CONFIG_FILE);

        $safeDeleteConfigDefinition = $container->getDefinition(self::SAFE_DELETE_CONFIG_NAMESPACE);

        $argument = [
            self::CONFIG_ATTRIBUTE => [
                self::CONFIG_INCLUDE_CHILDREN_KEY => $config[self::CONFIG_INCLUDE_CHILDREN_KEY],
            ]
        ];

        $safeDeleteConfigDefinition->setArguments($argument);
    }

    /**
     * @return string
     */
    public function getAlias(): string
    {
        return self::BUNDLE_ALIAS;
    }
}
