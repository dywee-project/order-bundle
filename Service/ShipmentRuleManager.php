<?php

namespace Dywee\OrderBundle\Service;

use Doctrine\ORM\EntityManager;
use Dywee\OrderBundle\Entity\BaseOrder;
use Dywee\OrderBundle\Entity\Shipment;
use Dywee\OrderBundle\Entity\ShipmentElement;
use Dywee\OrderBundle\Entity\ShipmentRule;

class ShipmentRuleManager
{
    /** @var \Doctrine\ORM\EntityRepository|\Dywee\OrderBundle\Repository\ShipmentRuleRepository */
    protected $repository;

    /** @var  BaseOrder */
    protected $order;

    /**
     * ShipmentRuleManager constructor.
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->repository = $em->getRepository(ShipmentRule::class);
    }

    /**
     * @param BaseOrder $order
     *
     * @return bool
     */
    public function handleOrder(BaseOrder $order)
    {
        $this->order = $order;

        $mustBeUnique = $this->handleUniqProduct();
        $this->handleQuantity();
        $this->handleWeight();

        if (!$mustBeUnique) {
            $this->reconciliateShipmentElements();
        }

        return true;
    }

    protected function reconciliateShipmentElements()
    {
    }

    protected function ruleOnWeight()
    {
    }

    /**
     * @param $valueToCheck
     * @param $operator
     * @param $value
     *
     * @return bool
     */
    protected function checkRule($valueToCheck, $operator, $value)
    {
        switch ($operator) {
            case '<':
                return $valueToCheck < $value;
            case '<=':
                return $valueToCheck <= $value;
            case '===';
            case '==';
            case '=';
                return $valueToCheck === $value;
            case '>':
                return $valueToCheck > $value;
            case '>=':
                return $valueToCheck >= $value;
            default:
                return false;
        }
    }

    protected function handleWeight()
    {
    }


    protected function handleQuantity()
    {
        /** @var ShipmentRule[] $rules */
        $rules = $this->repository->findForQuantityMax(['mappedKey' => 'quantity', 'operator' => '<']);

        if (count($rules) === 0) {
            return;
        }

        $max = 0;

        if (count($rules) === 1) {
            $max = $rules[0]->getValue();
        } else {
            foreach ($rules as $rule) {
                if ($rule->getValue() < $max) {
                    $max = $rule->getValue();
                }
            }
        }

        $shipmentToAdd = new Shipment();
        foreach ($this->order->getShipments() as $shipment) {
            if (!$shipment->getSendingIndex() || $shipment->getSendingIndex() === 1) {
                $shipmentToAdd = clone $shipment;
                break;
            }
        }

        foreach ($this->order->getShipments() as $shipment) {
            if ($shipment->countElements() > $max) {
                foreach ($shipment->getShipmentElements() as $shipmentElement) {
                    if ($shipmentElement->getQuantity() > $max) {
                        $rest = $shipmentElement->getQuantity() - $max;
                        $shipmentElement->setQuantity($max)->setWeight($shipmentElement->getProduct()->getWeight() * $max);
                        $shipment->canBeReconciliated();

                        while ($rest >= $max) {
                            $shipmentElementToAdd = new ShipmentElement();
                            $shipmentElementToAdd
                                ->setProduct($shipmentElement->getProduct())
                                ->setQuantity($max - $shipmentToAdd->countElements())
                                ->setWeight($shipmentElementToAdd->getProduct()->getWeight() * $max);
                            $shipmentToAdd->addShipmentElement($shipmentElementToAdd);
                            $shipmentToAdd->canBeReconciliated();
                            $rest -= $shipmentToAdd->countElements();
                            $this->order->addShipment($shipmentToAdd);
                            $shipmentToAdd = clone $shipment;
                        }

                        if ($rest > 0) {
                            $shipmentElementToAdd = clone $shipmentElement;
                            $shipmentElementToAdd
                                ->setProduct($shipmentElement->getProduct())
                                ->setQuantity($rest)
                                ->setWeight($shipmentElementToAdd->getProduct()->getWeight() * $rest);
                            $shipmentToAdd->addShipmentElement($shipmentElementToAdd);
                        }
                    }

                    if ($shipmentToAdd->countElements() === $max) {
                        $this->order->addShipment($shipmentToAdd);
                        $shipmentToAdd = clone $shipmentToAdd;
                    }
                }
            }

            //If the first manipulation does'nt resolve the rule
            if ($shipment->countElements() > $max) {
                $quantity = 0;
                foreach ($shipment->getShipmentElements() as $shipmentElement) {
                    $quantity += $shipmentElement->getQuantity();
                    if ($quantity > $max) {
                        $quantity = $shipmentElement->getQuantity();
                        $shipmentToAdd = new Shipment();
                    }
                }
            }
        }

        if ($shipmentToAdd->countElements() > 0) {
            $this->order->addShipment($shipmentToAdd);
        }
    }

    /**
     * @return bool
     */
    protected function handleUniqProduct()
    {
        $rule = $this->repository->findOneBy(['mappedKey' => 'product', 'operator' => '==', 'value' => '[self]']);

        if (!$rule) {
            return false;
        }

        // We link one and only one product to one shipment
        // TODO check why we have $product = null and if $product != null after that
        foreach ($this->order->getShipments() as $shipment) {
            $product = null;
            foreach ($shipment->getShipmentElements() as $shipmentElement) {
                if ($product !== null && $shipmentElement->getProduct() !== $product) {
                    $shipment->removeShipmentElement($shipmentElement);

                    $shipmentToAdd = new Shipment();
                    $shipmentToAdd->addShipmentElement($shipmentElement);
                }
            }
        }

        return true;
    }
}
