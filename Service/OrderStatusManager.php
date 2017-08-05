<?php
/**
 * Created by PhpStorm.
 * User: Olivier
 * Date: 17/04/17
 * Time: 19:20
 */

namespace Dywee\OrderBundle\Service;


use Dywee\OrderBundle\Entity\BaseOrderInterface;
use Payum\Core\Request\GetHumanStatus;

class OrderStatusManager
{
    public function changePaymentStatus(BaseOrderInterface $order, GetHumanStatus $status)
    {
        switch (true) {
            case $status->isAuthorized():
                $order->setPaymentStatus(BaseOrderInterface::PAYMENT_AUTHORIZED);
                $this->handleValidPayment($order);
                break;
            case $status->isCaptured():
                $order->setPaymentStatus(BaseOrderInterface::PAYMENT_VALIDATED);
                $this->handleValidPayment($order);
                break;
            case $status->isCanceled():
            case $status->isExpired():
            case $status->isFailed():
            case $status->isUnknown():
                $order->setPaymentStatus(BaseOrderInterface::PAYMENT_CANCELLED);
                break;
            case $status->isPending():
                $order->setPaymentStatus(BaseOrderInterface::PAYMENT_WAITING_VALIDATION);
                break;
        }
    }

    private function handleValidPayment(BaseOrderInterface $order)
    {
        if ($order->getState() === BaseOrderInterface::STATE_IN_SESSION) {
            $order->setPaymentStatus(BaseOrderInterface::PAYMENT_VALIDATED);
            $order->setState(BaseOrderInterface::STATE_IN_PROGRESS);
        }
    }
}