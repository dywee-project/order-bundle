<?php

namespace Dywee\OrderBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('dywee_order');

        $rootNode
            ->children()
            ->enumNode('price_vat_display')
                ->values(['incl', 'excl'])
                ->defaultValue('incl')
            ->end()
            ->enumNode('sell_type')
                ->values(['buy', 'rent', 'both'])
                ->defaultValue('buy')
            ->end()
            ->enumNode('auth_strategy')
                ->values(['anon', 'registered', 'both'])
                ->defaultValue('both')
            ->end()
            ->booleanNode('in_sidebar')
                ->defaultTrue()
            ->end();

        return $treeBuilder;
    }
}
