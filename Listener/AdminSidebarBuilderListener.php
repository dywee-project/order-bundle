<?php

namespace Dywee\OrderBundle\Listener;

use Dywee\CoreBundle\DyweeCoreEvent;
use Dywee\CoreBundle\Event\SidebarBuilderEvent;
use Dywee\OrderBundle\Service\AdminSidebarHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class AdminSidebarBuilderListener implements EventSubscriberInterface
{
    /** @var AdminSidebarHandler $orderAdminSidebarHandler */
    private $orderAdminSidebarHandler;

    /** @var bool */
    private $inSidebar;

    public function __construct(AdminSidebarHandler $orderAdminSidebarHandler, bool $inSidebar)
    {
        $this->orderAdminSidebarHandler = $orderAdminSidebarHandler;
        $this->inSidebar = $inSidebar;
    }

    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return [
            DyweeCoreEvent::BUILD_ADMIN_SIDEBAR => ['addElementToSidebar', -20],
        ];
    }

    public function addElementToSidebar(SidebarBuilderEvent $adminSidebarBuilderEvent)
    {
        if($this->inSidebar) {
            $adminSidebarBuilderEvent->addElement($this->orderAdminSidebarHandler->getSideBarMenuElement());
            $adminSidebarBuilderEvent->addElement($this->orderAdminSidebarHandler->getShipmentSidebarElements());
        }
    }

    /**
     * @return bool
     */
    public function isInSidebar() : bool
    {
        return $this->inSidebar;
    }

}