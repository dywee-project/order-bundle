<?php

namespace Dywee\OrderBundle\Service;

use Doctrine\ORM\EntityManager;
use Dywee\AddressBundle\Entity\CountryInterface;
use Dywee\OrderBundle\Entity\BaseOrder;
use Dywee\OrderBundle\Entity\BaseOrderInterface;
use Dywee\OrderBundle\Entity\Shipment;

class ShippingMethod
{
    protected $em;
    protected $error;
    protected $shipmentMethods = [];
    protected $shipmentMethodRepository;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
        $this->shipmentMethodRepository = $this->em->getRepository(\Dywee\OrderBundle\Entity\ShippingMethod::class);
    }

    public function calculateForOrder(BaseOrder $order)
    {
        $shipmentMethods = [];
        if (!$order->getShippingAddress()) {
            throw new \InvalidArgumentException('shipmentMethod.error.no_shipping_address');
        }

        $country = $order->getShippingAddress()->getCity()->getCountry();

        foreach ($order->getShipments() as $shipment) {
            $this->getAvailableShipmentMethods($shipment, $country);

            $shipmentMethods = [];

            if (count($this->shipmentMethods) < 1) {
                throw new \Exception('shipmentMethod.error.no_method_found');
            } elseif (count($this->shipmentMethods) === 1) {
                if (array_key_exists($this->shipmentMethods[0]->getId(), $shipmentMethods)) {
                    $shipmentMethods[$shipmentMethods[0]->getId()]['total'] += $shipmentMethods[0]->getPrice();
                } else {
                    $shipmentMethods[$this->shipmentMethods[0]->getId()] = [
                        'shipment' => $this->shipmentMethods[0],
                        'total'    => $this->shipmentMethods[0]->getPrice()
                    ];
                }
            } else {
                foreach ($this->shipmentMethods as $shipmentMethod) {
                    if (array_key_exists($shipmentMethod->getId(), $shipmentMethods)) {
                        $shipmentMethods[$shipmentMethod->getId()]['total'] += $shipmentMethod->getPrice();
                    } else {
                        $shipmentMethods[$shipmentMethod->getId()] = [
                            'shipment' => $shipmentMethod,
                            'total'    => $shipmentMethod->getPrice()
                        ];
                    }
                }
            }
        }

        $toDisplay = [];
        $shipments = count($order->getShipments());

        foreach ($shipmentMethods as $shipmentMethod) {
            if ($shipments === 1) {
                $toDisplay[$shipmentMethod['shipment']->getNameWithPrice()] = $shipmentMethod['shipment']->getId();
            } else {
                $toDisplay[$shipmentMethod['shipment']->getNameWithPrice() . ' par envoi'] = $shipmentMethod['shipment']->getId();
            }
        }

        return $toDisplay;
    }

    protected function getAvailableShipmentMethods(Shipment $shipment, CountryInterface $country)
    {
        $weight = $shipment->getWeight();
        $shipmentMethods = $this->shipmentMethodRepository->findForCheckout($country, $weight);

        if (count($this->shipmentMethods) === 0) {
            $this->shipmentMethods = $shipmentMethods;
        } else {
            //TODO virer ceux qu'on ne peut pas utiliser
        }

        return $this->shipmentMethods;
    }

    public function setNoShippingMethod(BaseOrderInterface $order)
    {
        $shippingMethodRepository = $this->em->getRepository(\Dywee\OrderBundle\Entity\ShippingMethod::class);
        $shippingMethod = $shippingMethodRepository->findOneByType('free');
        $order->setShippingMethod($shippingMethod);
    }
}