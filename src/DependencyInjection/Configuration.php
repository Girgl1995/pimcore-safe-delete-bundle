<?php

declare(strict_types=1);

namespace Factotum\SafeDeleteBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    private const INCLUDE_CHILDREN_KEY = 'include_children';

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder(Extension::BUNDLE_ALIAS);
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
            ->booleanNode(self::INCLUDE_CHILDREN_KEY)
            ->defaultValue(false)
            ->end()
            ->end();

        return $treeBuilder;
    }
}
