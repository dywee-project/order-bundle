<?php

namespace Dywee\OrderBundle\Service;

use Dywee\OrderBundle\Entity\BaseOrder;
use Symfony\Component\Routing\Router;

class AdminSidebarHandler
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
            'icon' => 'fa fa-shopping-cart',
            'label' => 'order.sidebar.label',
            'children' => array(
                array(
                    'icon' => 'fa fa-list-alt',
                    'label' => 'order.sidebar.table',
                    'route' => $this->router->generate('order_adminList', array('state' => BaseOrder::STATE_IN_PROGRESS))
                ),
                array(
                    'icon' => 'fa fa-cogs',
                    'label' => 'order.sidebar.invoice',
                    'route' => $this->router->generate('order_reference')
                ),
            )
        );

        return $menu;
    }
}