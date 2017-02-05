<?php

namespace Dywee\OrderBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $treeBuilder->root('dywee_order')
            ->children()
            ->booleanNode('is_price_ttc')
                ->defaultValue(true)
                ->end()
            ->enumNode('sell_type')
                ->values(['buy', 'rent', 'both'])
                ->cannotBeEmpty()
                ->defaultValue('buy')
                ->end()
            ->enumNode('order_connexion_permission')
                ->values(['anon', 'registered', 'both'])
                ->defaultValue('both')
                ->cannotBeEmpty()
                ->defaultValue('Hackzilla\Bundle\TicketBundle\Entity\TicketMessage')
                ->end()
            ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}