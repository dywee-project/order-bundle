<?php

namespace Dywee\OrderBundle\Listener;

use Dywee\CoreBundle\DyweeCoreEvent;
use Dywee\CoreBundle\Event\AdminSidebarBuilderEvent;
use Dywee\OrderBundle\Service\AdminSidebarHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class AdminSidebarBuilderListener implements EventSubscriberInterface
{
    private $orderAdminSidebarHandler;

    public function __construct(AdminSidebarHandler $orderAdminSidebarHandler)
    {
        $this->orderAdminSidebarHandler = $orderAdminSidebarHandler;
    }

    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return [
            DyweeCoreEvent::BUILD_ADMIN_SIDEBAR => ['addElementToSidebar', -10]
        ];
    }

    public function addElementToSidebar(AdminSidebarBuilderEvent $adminSidebarBuilderEvent)
    {
        $adminSidebarBuilderEvent->addAdminElement($this->orderAdminSidebarHandler->getSideBarMenuElement());
        $adminSidebarBuilderEvent->addAdminElement($this->orderAdminSidebarHandler->getShipmentSidebarElements());
    }

}