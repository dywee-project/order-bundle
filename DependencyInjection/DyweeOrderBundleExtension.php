<?php
/**
 * Created by PhpStorm.
 * User: Olivier
 * Date: 20/04/17
 * Time: 07:16
 */

namespace Dywee\OrderBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class DyweeOrderBundleExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');


        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $def = $container->getDefinition('dywee_order.admin_sidebar_listener');
        $def->replaceArgument(1, $config['in_sidebar']);
    }
}