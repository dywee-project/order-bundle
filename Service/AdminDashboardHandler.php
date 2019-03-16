<?php

namespace Dywee\OrderBundle\Service;

use Symfony\Component\Routing\RouterInterface;

class AdminDashboardHandler
{

    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function getDashboardElement()
    {
        $elements = [
            'key'   => 'order',
            'cards' => [
                [
                    'controller' => 'DyweeOrderBundle:Dashboard:card'
                ]
            ],
            'boxes' => [
                [
                    'column' => 'col-md-8',
                    'type'   => 'default',
                    'title'  => 'order.dashboard.table',
                    'body'   => [
                        [
                            'boxBody'    => false,
                            'controller' => 'DyweeOrderBundle:Dashboard:table'
                        ]
                    ]
                ]
            ]
        ];

        return $elements;
    }
}