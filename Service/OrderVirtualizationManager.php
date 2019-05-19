<?php
/**
 * Created by PhpStorm.
 * User: Olivier
 * Date: 9/05/17
 * Time: 08:46
 */

namespace Dywee\OrderBundle\Service;


use Dywee\OrderBundle\Entity\BaseOrderInterface;
use Dywee\OrderBundle\Entity\OrderElement;
use Dywee\ProductBundle\Model\VirtualProductInterface;

class OrderVirtualizationManager
{
    /**
     * @param BaseOrderInterface $order
     *
     * @return bool
     */
    public function containsVirtualProduct(BaseOrderInterface $order)
    {
        return $this->calculVirtualization($order, false);
    }

    /**
     * @param BaseOrderInterface $order
     *
     * @return bool
     */
    public function isFullyVirtual(BaseOrderInterface $order)
    {
        return $this->calculVirtualization($order);
    }

    /**
     * @param BaseOrderInterface $order
     * @param bool               $exclusive
     *
     * @return bool
     */
    public function calculVirtualization(BaseOrderInterface $order, $exclusive = true)
    {
        $isVirtual = count($order->getOrderElements()) > 0;
        $containsVirtual = false;

        /** @var OrderElement $element */
        foreach ($order->getOrderElements() as $element) {
            if ($element->getProduct() instanceof VirtualProductInterface) {
                $containsVirtual = true;
            } else {
                $isVirtual = false;
            }
        }

        return $exclusive ? $isVirtual : $containsVirtual;
    }
}
