<?php

namespace Dywee\OrderBundle\Service;

use Symfony\Component\Routing\Router;

class OrderAdminSidebarHandler{

    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function getSideBarMenuElement()
    {
        $menu = array(
            'icon' => 'fa fa-files-o',
            'label' => 'Commandes',
            'children' => array(
                array(
                    'icon' => 'fa fa-list-alt',
                    'label' => 'Gestion des commandes',
                    'route' => $this->router->generate('order_table')
                ),
                array(
                    'icon' => 'fa fa-cog',
                    'label' => 'Gestion des références facture',
                    'route' => $this->router->generate('order_reference_update')
                ),
            )
        );

        return $menu;
    }
}