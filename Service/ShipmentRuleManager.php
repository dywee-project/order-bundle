<?php

namespace Dywee\OrderBundle\Service;

use Doctrine\ORM\EntityManager;
use Dywee\OrderBundle\Entity\BaseOrder;
use Dywee\ShipmentBundle\Entity\Shipment;
use Dywee\ShipmentBundle\Entity\ShipmentElement;

class ShipmentRuleManager{
    protected $repository;
    protected $order;

    public function __construct(EntityManager $em)
    {
        $this->repository = $em->getRepository('DyweeShipmentBundle:ShipmentRule');
    }

    public function handleOrder(BaseOrder $order)
    {
        $this->order = $order;

        $mustBeUniq = $this->handleUniqProduct();
        $this->handleQuantity();
        $this->handleWeight();

        if(!$mustBeUniq)
            $this->reconciliateShipmentElements();

        return true;
    }

    protected function reconciliateShipmentElements()
    {

    }

    protected function ruleOnWeight()
    {

    }

    protected function checkRule($valueToCheck, $operator, $value)
    {
        switch($operator)
        {
            case '<':
                return $valueToCheck < $value;
            case '<=':
                return $valueToCheck <= $value;
            case '==';
            case '=';
                return $valueToCheck == $value;
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
        $rules = $this->repository->findForQuantityMax(array('mappedKey' => 'quantity', 'operator' => '<'));

        if(count($rules) === 0)
            return;

        $max = 0;

        if(count($rules) === 1)
            $max = $rules[0]->getValue();
        else{
            foreach($rules as $rule)
                if($rule->getValue() < $max)
                    $max = $rule->getValue();
        }

        $shipmentToAdd = new Shipment();
        foreach($this->order->getShipments() as $shipment)
        {
            if(!$shipment->getSendingIndex() || $shipment->getSendingIndex() == 1)
            {
                $shipmentToAdd = clone $shipment;
                break;
            }
        }

        foreach($this->order->getShipments() as $shipment)
        {
            if($shipment->countElements() > $max)
            {
                foreach($shipment->getShipmentElements() as $shipmentElement)
                {
                    if($shipmentElement->getQuantity() > $max)
                    {
                        $rest = $shipmentElement->getQuantity() - $max;
                        $shipmentElement->setQuantity($max)->setWeight($shipmentElement->getProduct()->getWeight() * $max);
                        $shipment->canBeReconciliated(false);

                        while($rest >= $max)
                        {
                            $shipmentElementToAdd = clone $shipmentElement;
                            $shipmentElementToAdd
                                ->setProduct($shipmentElement->getProduct())
                                ->setQuantity($max - $shipmentToAdd->countElements())
                                ->setWeight($shipmentElementToAdd->getProduct()->getWeight() * $max)
                            ;
                            $shipmentToAdd->addShipmentElement($shipmentElementToAdd);
                            $shipmentToAdd->canBeReconciliated(false);
                            $rest -= $shipmentToAdd->countElements();
                            $this->order->addShipment($shipmentToAdd);
                            $shipmentToAdd = clone $shipmentToAdd;
                        }

                        if($rest > 0)
                        {
                            $shipmentElementToAdd = clone $shipmentElement;
                            $shipmentElementToAdd
                                ->setProduct($shipmentElement->getProduct())
                                ->setQuantity($rest)
                                ->setWeight($shipmentElementToAdd->getProduct()->getWeight() * $rest)
                            ;
                            $shipmentToAdd->addShipmentElement($shipmentElementToAdd);
                        }
                    }

                    if($shipmentToAdd->countElements() === $max)
                    {
                        $this->order->addShipment($shipmentToAdd);
                        $shipmentToAdd = clone $shipmentToAdd;
                    }
                }
            }

            //If the first manipulation does'nt resolve the rule
            if($shipment->countElements() > $max)
            {
                $quantity = 0;
                foreach($shipment as $shipmentElement)
                {
                    $quantity += $shipmentElement->getQuantity();
                    if($quantity > $max)
                    {
                        $quantity = $shipmentElement->getQuantity();
                        $shipmentToAdd = new Shipment();
                    }
                }
            }
        }
        if($shipmentToAdd->countElements() > 0)
            $this->order->addShipment($shipmentToAdd);
    }

    protected function handleUniqProduct()
    {
        $rule = $this->repository->findOneBy(array('mappedKey' => 'product', 'operator' => '==', 'value' => '[self]'));

        if(!$rule)
            return false;

        //We rely one and only one product to one shipment
        foreach($this->order->getShipments() as $shipment)
        {
            $product = null;
            foreach($shipment->getShipmentElements() as $shipmentElement)
            {
                if($shipmentElement->getProduct() != $product && $product != null)
                {
                    $shipment->removeShipmentElement($shipmentElement);

                    $shipmentToAdd = new Shipment();
                    $shipmentToAdd->addShipmentElement($shipmentElement);
                }
            }
        }

        return true;
    }
}