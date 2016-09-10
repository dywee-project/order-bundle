<?php

namespace Dywee\OrderBundle\Service;

use Symfony\Component\Routing\Router;

class OrderAdminDashboardHandler
{

    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function getDashboardElement()
    {
        $elements = array(
            'key' => 'order',
            'boxes' => array(
                array(
                    'column' => 'col-md-8',
                    'type' => 'default',
                    'title' => 'order.dashboard.table',
                    'body' => array(
                        array(
                            'boxBody' => false,
                            'controller' => 'DyweeOrderBundle:Dashboard:table'
                        )
                    )
                )
            )
        );

        return $elements;
    }
}