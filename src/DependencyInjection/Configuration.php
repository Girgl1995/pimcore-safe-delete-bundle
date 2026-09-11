<?php

declare(strict_types=1);

namespace Factotum\SafeDeleteBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    private const KEY_INCLUDE_CHILDREN = 'include_children';

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder(Extension::BUNDLE_ALIAS);
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
            ->booleanNode(self::KEY_INCLUDE_CHILDREN)
            ->defaultValue(false)
            ->end()
            ->end();

        return $treeBuilder;
    }
}
