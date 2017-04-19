<?php
/**
 * Created by PhpStorm.
 * User: Olivier
 * Date: 18/04/17
 * Time: 08:35
 */

namespace Dywee\OrderBundle\Listener\Payment;

use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PaymentAuthorizeListener implements EventSubscriberInterface
{
    public function guardAuthorize(GuardEvent $event)
    {
        $order = $event->getSubject();

        if (empty($title)) {
            // Posts with no title should not be allowed
            $event->setBlocked(true);
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            'workflow.blogpost.guard.to_review' => array('guardAuthorize'),
        );
    }
}