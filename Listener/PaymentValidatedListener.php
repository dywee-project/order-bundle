<?php
/**
 * Created by PhpStorm.
 * User: Olivier
 * Date: 24/05/17
 * Time: 18:40
 */

namespace Dywee\OrderBundle\Listener;


use Dywee\OrderBundle\Event\PaymentValidatedEvent;
use Symfony\Component\HttpFoundation\Session\Session;

class PaymentValidatedListener
{
    /**
     * @var Session
     */
    private $session;

    /**
     * PaymentValidatedListener constructor.
     *
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @param PaymentValidatedEvent $event
     */
    public function onPaymentValidated(PaymentValidatedEvent $event)
    {
        $this->session->set('order', null);
        $this->session->set('validatedOrderId', $event->getOrder()->getId());
    }
}
