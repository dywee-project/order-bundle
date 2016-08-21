<?php

namespace Dywee\OrderBundle\Service;

use Dywee\OrderBundle\Entity\BaseOrder;
use Dywee\ShipmentBundle\Entity\Shipment;
use Dywee\ShipmentBundle\Entity\ShipmentElement;

class ShipmentCalculator
{
    public function calculateShipments(BaseOrder $order)
    {
        if(count($order->getOrderElements()) == 0)
            return;

        $order->setShipments(array());

        $shipment = new Shipment();
        $departureDate = $order->getValidatedAt() ?? new \DateTime();

        $shipment->setDepartureAt($departureDate);
        $shipment->setState(Shipment::STATE_NOT_PREPARED);

        //TODO gérer abonnement
        //TODO gérer produits téléchargeables
        //TODO gérer règles de poids max par colis


        foreach($order->getOrderElements() as $orderElement)
        {
            for($j = 0; $j < $orderElement->getQuantity(); $j++) {
                $shipmentElement = new ShipmentElement();
                $shipmentElement->setQuantity($orderElement->getQuantity());
                $shipmentElement->setProduct($orderElement->getProduct());
                $shipmentElement->setWeight($orderElement->getProduct()->getWeight() * $orderElement->getQuantity());
                $shipment->addShipmentElement($shipmentElement);
            }
        }

        $order->addShipment($shipment);
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