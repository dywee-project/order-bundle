<?php

namespace Dywee\OrderBundle\Service;


use Dywee\CoreBundle\Model\ProductInterface;
use Dywee\OrderBundle\Entity\BaseOrder;
use Dywee\OrderBundle\Entity\OrderElement;

class OrderElementManager
{
    /**
     * @param BaseOrder        $order
     * @param ProductInterface $product
     * @param                  $quantity
     * @param int              $locationCoeff
     */
    public function addProduct(BaseOrder $order, ProductInterface $product, $quantity, $locationCoeff = 1)
    {
        $exist = false;
        // Check if the product has been ordered at least once
        foreach ($order->getOrderElements() as $key => $orderElement) {
            if ($orderElement->getProduct()->getId() === $product->getId()) {
                // If yes, we increment the quantity
                $orderElement->setQuantity($orderElement->getQuantity() + $quantity);
                if ($orderElement->getQuantity() <= 0) {
                    $order->removeOrderElement($orderElement);
                }
                $exist = $key;
                break;
            }
        }

        // If not, we add it
        if (!is_numeric($exist)) {
            $orderElement = new OrderElement();

            $orderElement->setProduct($product);
            $orderElement->setQuantity($quantity);
            $orderElement->setLocationCoeff($locationCoeff);
            $orderElement->setUnitPrice($product->getPrice());

            $order->addOrderElement($orderElement);
        }

        $order->forcePriceCalculation();
        $order->setMustRecalculShipments(true);
    }

    /**
     * @param BaseOrder        $order
     * @param ProductInterface $product
     * @param int              $quantity
     */
    public function removeProduct(BaseOrder $order, ProductInterface $product, int $quantity = 0)
    {
        if (!$quantity) {
            $this->addProduct($order, $product, -1 * $this->countProductQuantity($order, $product));
        } elseif ($quantity < 0) {
            $this->addProduct($order, $product, $quantity);
        } else {
            $this->addProduct($order, $product, -1 * $quantity);
        }
    }

    public function updateProductQuantity(BaseOrder $order, ProductInterface $product, $quantity)
    {
        // Check if the product has been ordered at least once
        foreach ($order->getOrderElements() as $key => $orderElement) {
            if ($orderElement->getProduct()->getId() === $product->getId()) {
                // If yes, we increment the quantity
                $orderElement->setQuantity($quantity);

                $order->forcePriceCalculation();
                $order->setMustRecalculShipments(true);

                break;
            }
        }
    }

    /**
     * @param BaseOrder        $order
     * @param ProductInterface $product
     *
     * @return int
     */
    public function countProductQuantity(BaseOrder $order, ProductInterface $product)
    {
        foreach ($order->getOrderElements() as $orderElement) {
            if ($orderElement->getProduct()->getId() === $product->getId()) {
                return $orderElement->getQuantity();
            }
        }

        return 0;
    }
}
