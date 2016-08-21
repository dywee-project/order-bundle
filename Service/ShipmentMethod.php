<?php

namespace Dywee\OrderBundle\Service;

use Doctrine\ORM\EntityManager;

class ShipmentMethod
{
    protected $em;
    protected $error;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function calculateForOrder($order)
    {
        $shipmentMethods = array();
        if(!$order->getShippingAddress())
        {
            $this->error = 'shipmentMethod.error.no_shipping_address';
            return false;
        }
        $country = $order->getShippingAddress()->getCity()->getCountry();
        $shipmentMethodRepository = $this->em->getRepository('DyweeShipmentBundle:ShipmentMethod');

        foreach($order->getShipments() as $shipment)
        {
            $weight = $shipment->getWeight();
            $shipments = $shipmentMethodRepository->findForCheckout($country, $weight);

            if(count($shipments) < 1){
                $this->error = 'shipmentMethod.error.no_method_found';
                return false;
            }
            elseif(count($shipments) == 1)
            {
                $shipmentMethods[$shipments[0]->getNameWithPrice()] = $shipments[0]->getId();
            }
            else{
                foreach($shipments as $shipmentMethod)
                {
                    $shipmentMethods[$shipmentMethod->getNameWithPrice()] = $shipmentMethod->getId();
                }
            }
        }
        return $shipmentMethods;
    }

    public function getError()
    {
        return $this->error;
    }
}