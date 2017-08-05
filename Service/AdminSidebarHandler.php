<?php

namespace Dywee\OrderBundle\Service;

use Dywee\OrderBundle\Entity\BaseOrder;
use Symfony\Component\Routing\Router;

class AdminSidebarHandler
{
    /** @var Router */
    private $router;

    /**
     * AdminSidebarHandler constructor.
     *
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @return array
     */
    public function getSideBarMenuElement()
    {
        return [
            'key'      => 'order',
            'icon'     => 'fa fa-shopping-cart',
            'label'    => 'order.sidebar.label',
            'children' => [
                [
                    'icon'  => 'fa fa-list-alt',
                    'label' => 'order.sidebar.table',
                    'route' => $this->router->generate('order_table', ['state' => BaseOrder::STATE_IN_PROGRESS])
                ],
                [
                    'icon'  => 'fa fa-cogs',
                    'label' => 'order.sidebar.invoice',
                    'route' => $this->router->generate('order_reference')
                ],
            ]
        ];
    }

    /**
     * @return array
     */
    public function getShipmentSidebarElements()
    {

        return [
            'key'      => 'shipment',
            'icon'     => 'fa fa-truck',
            'label'    => 'shipment.sidebar.label',
            'children' => [
                [
                    'icon'  => 'fa fa-list-alt',
                    'label' => 'deliver.sidebar.table',
                    'route' => $this->router->generate('deliver_table')
                ],
                [
                    'icon'  => 'fa fa-list-alt',
                    'label' => 'shipment.sidebar.table',
                    'route' => $this->router->generate('shipping_method_table')
                ],
                [
                    'icon'  => 'fa fa-list-alt',
                    'label' => 'shipmentRule.sidebar.table',
                    'route' => $this->router->generate('shipment_rule_table')
                ],
            ]
        ];
    }
}