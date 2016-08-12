<?php

namespace Dywee\OrderBundle\Service;

use Symfony\Component\Routing\Router;

class OrderAdminSidebarHandler
{

    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function getSideBarMenuElement()
    {
        $menu = array(
            'key' => 'order',
            'icon' => 'fa fa-files-o',
            'label' => 'order.sidebar.label',
            'children' => array(
                array(
                    'icon' => 'fa fa-list-alt',
                    'label' => 'order.sidebar.table',
                    'route' => $this->router->generate('order_table')
                ),
                array(
                    'icon' => 'fa fa-cog',
                    'label' => 'order.sidebar.invoice',
                    'route' => $this->router->generate('order_reference_update')
                ),
            )
        );

        return $menu;
    }
}