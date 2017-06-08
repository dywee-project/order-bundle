<?php
/**
 * Created by PhpStorm.
 * User: Olivier
 * Date: 24/05/17
 * Time: 18:35
 */

namespace Dywee\OrderBundle\Event;


use Dywee\OrderBundle\Entity\BaseOrderInterface;
use Symfony\Component\EventDispatcher\Event;

class PaymentValidatedEvent extends Event
{
    /** @var  BaseOrderInterface */
    private $order;

    public function __construct(BaseOrderInterface $order)
    {
        $this->order = $order;
    }

    /**
     * @return BaseOrderInterface
     */
    public function getOrder()
    {
        return $this->order;
    }
}