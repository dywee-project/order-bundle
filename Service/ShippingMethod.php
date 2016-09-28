<?php

namespace Dywee\OrderBundle\Service;

use Doctrine\ORM\EntityManager;
use Dywee\AddressBundle\Entity\Country;
use Dywee\AddressBundle\Entity\CountryInterface;
use Dywee\OrderBundle\Entity\BaseOrder;
use Dywee\ShipmentBundle\Entity\Shipment;

class ShippingMethod
{
    protected $em;
    protected $error;
    protected $shipmentMethods = array();
    protected $shipmentMethodRepository;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
        $this->shipmentMethodRepository = $this->em->getRepository('DyweeShipmentBundle:ShippingMethod');
    }

    public function calculateForOrder(BaseOrder $order)
    {
        $shipmentMethods = array();
        if(!$order->getShippingAddress())
        {
            $this->error = 'shipmentMethod.error.no_shipping_address';
            return false;
        }

        $country = $order->getShippingAddress()->getCity()->getCountry();

        foreach($order->getShipments() as $shipment)
        {
            $this->getAvailableShipmentMethods($shipment, $country);

            $shipmentMethods = array();

            if(count($this->shipmentMethods) < 1){
                $this->error = 'shipmentMethod.error.no_method_found';
                return false;
            }
            elseif(count($this->shipmentMethods) === 1) {
                if(array_key_exists($this->shipmentMethods[0]->getId(), $shipmentMethods))
                    $shipmentMethods[$shipmentMethods[0]->getId()]['total'] += $shipmentMethods[0]->getPrice();
                else
                    $shipmentMethods[$this->shipmentMethods[0]->getId()] = array(
                        'shipment' => $this->shipmentMethods[0],
                        'total' => $this->shipmentMethods[0]->getPrice()
                    );
            }
            else{
                foreach($this->shipmentMethods as $shipmentMethod)
                {
                    if(array_key_exists($shipmentMethod->getId(), $shipmentMethods))
                        $shipmentMethods[$shipmentMethod->getId()]['total'] += $shipmentMethod->getPrice();
                else
                    $shipmentMethods[$shipmentMethod->getId()] = array(
                        'shipment' => $shipmentMethod,
                        'total' => $shipmentMethod->getPrice()
                    );
                }
            }
        }

        $toDisplay = array();
        $shipments = count($order->getShipments());

        foreach($shipmentMethods as $shipmentMethod){
            if($shipments === 1)
                $toDisplay[$shipmentMethod['shipment']->getNameWithPrice()] = $shipmentMethod['shipment']->getId();
            else
                $toDisplay[$shipmentMethod['shipment']->getNameWithPrice(). ' par envoi'] = $shipmentMethod['shipment']->getId();
        }

        return $toDisplay;
    }

    public function getError()
    {
        return $this->error;
    }

    protected function getAvailableShipmentMethods(Shipment $shipment, CountryInterface $country)
    {
        $weight = $shipment->getWeight();
        $shipmentMethods = $this->shipmentMethodRepository->findForCheckout($country, $weight);

        if(count($this->shipmentMethods) === 0)
            $this->shipmentMethods = $shipmentMethods;
        else{
            //TODO virer ceux qu'on ne peut pas utiliser
        }

        return $this->shipmentMethods;
    }
}