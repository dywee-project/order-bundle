<?php

namespace Dywee\OrderBundle\Service;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Dywee\OrderBundle\Entity\BaseOrder;
use Dywee\ProductBundle\Entity\ProductDownloadable;
use Dywee\ProductBundle\Entity\ProductSubscription;
use Dywee\ShipmentBundle\Entity\Shipment;
use Dywee\ShipmentBundle\Entity\ShipmentElement;

class ShipmentCalculator
{
    protected $shipmentRuleManager;
    protected $em;

    /*public function __construct(ShipmentRuleManager $ruleManager)
    {
        $this->shipmentRuleManager = $ruleManager;
    }*/

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if(!$entity instanceof BaseOrder)
            return;

        $this->em = $args->getEntityManager();
        return $this->calculateShipments($entity);
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if(!$entity instanceof BaseOrder)
            return;

        $this->em = $args->getEntityManager();
        return $this->calculateShipments($entity);
    }

    public function calculateShipments(BaseOrder $order)
    {
        if(!$order->mustRecalculShipments())
            return;

        $this->shipmentRuleManager = new ShipmentRuleManager($this->em);

        $order->setShipments(array());

        $shipment = new Shipment();
        $departureDate = $order->getValidatedAt() ?? new \DateTime();

        $shipment->setDepartureAt($departureDate);


        foreach($order->getOrderElements() as $orderElement)
        {
            $product = $orderElement->getProduct();
            //On gère les abonnements
            if($product instanceof ProductSubscription)
            {
                for($i = 0; $i < $product->getMaxShipment(); $i++)
                {
                    $shipmentForSubscription = new Shipment();
                    $shipmentElementForSubscription = new ShipmentElement();
                    $shipmentForSubscription->setSendingIndex($i+1)->addShipmentElement($shipmentElementForSubscription);

                    $departure = clone $departureDate;
                    if($i > 0)
                        $departure->modify('+'.$product->getRecurrence(). ' ' . $product->getRecurrenceUnit());

                    $shipmentForSubscription->setDepartureAt($departure);

                    $shipmentElementForSubscription->setQuantity($orderElement->getQuantity());
                    $shipmentElementForSubscription->setProduct($product);
                    $shipmentElementForSubscription->setWeight($product->getWeight() * $orderElement->getQuantity());

                    $order->addShipment($shipmentForSubscription);
                }
            }
            else if(!$product instanceof ProductDownloadable){

                $shipmentElement = new ShipmentElement();

                $shipmentElement->setQuantity($orderElement->getQuantity());
                $shipmentElement->setProduct($product);
                $shipmentElement->setWeight($product->getWeight() * $orderElement->getQuantity());
                $shipment->addShipmentElement($shipmentElement);
            }

        }


        $order->addShipment($shipment);
        $this->shipmentRuleManager->handleOrder($order);
        $order->forcePriceCalculation();
        $order->recalculShipmentsFinished();
    }
}

/*

            foreach($this->getOrderElements() as $orderElement)
            {
                //TODO: checker la condition
                //if($orderElement->getProduct()->getValidatedAt() > 1)
                //{
                    for($j = 0; $j < $orderElement->getQuantity(); $j++)
                    {
                        $shipment = new Shipment();
                        $departureDate = $this->getValidatedAt() == null ? new \DateTime() : $this->getValidatedAt();
                        $departureDate->modify('+1day');
                        $shipment->setDepartureDate($departureDate);
                        $shipment->setState(0);

                        $shipmentElement = new ShipmentElement();
                        $shipmentElement->setQuantity($orderElement->getQuantity());
                        $shipmentElement->setProduct($orderElement->getProduct());
                        $shipmentElement->setWeight(($orderElement->getProduct()->getWeight()*$orderElement->getQuantity()));
                        $shipment->addShipmentElement($shipmentElement);

                        $this->addShipment($shipment);

                        //if($orderElement->getProduct()->getValidatedAt() == 3)
                        //{
                            $shipment->setSendingIndex(1);

                            $departureDate = $this->getValidatedAt() == null ? new \DateTime() : $this->getValidatedAt();
                            $departureDay = (int) $departureDate->format('d');
                            $departureMonth = (int) $departureDate->format('m');
                            $departureYear = (int) $departureDate->format('Y');

                            if($departureDay > 20)
                                $departureMonth++;

                            $departureDay = 10;

                            for($i=1; $i< $orderElement->getProduct()->getRecurrence(); $i++)
                            {
                                $departureMonth++;
                                if($departureMonth > 12){
                                    $departureMonth -= 12;
                                    $departureYear++;
                                }

                                $shipment = new Shipment();

                                $departure = new \DateTime($departureYear.'/'.$departureMonth.'/'.$departureDay);
                                $shipment->setDepartureDate($departure);
                                $shipment->setState(0);
                                $shipment->setSendingIndex($i+1);

                                $shipmentElement = new ShipmentElement();
                                $shipmentElement->setQuantity($orderElement->getQuantity());
                                $shipmentElement->setProduct($orderElement->getProduct());
                                $shipmentElement->setWeight(($orderElement->getProduct()->getWeight()*$orderElement->getQuantity()));
                                $shipment->addShipmentElement($shipmentElement);

                                $this->addShipment($shipment);
                            }
                        //}
                    //}
                }
                /*elseif($orderElement->getProduct()->getValidatedAt() == 1)
                {
                    $shipmentElement = new ShipmentElement();
                    $shipmentElement->setQuantity($orderElement->getQuantity());
                    $shipmentElement->setProduct($orderElement->getProduct());
                    $shipmentElement->setWeight(($orderElement->getProduct()->getWeight()*$orderElement->getQuantity()));
                    $productShipment->addShipmentElement($shipmentElement);
                }
}

if(count($productShipment->getShipmentElements())>0)
    $this->addShipment($productShipment);
}*/