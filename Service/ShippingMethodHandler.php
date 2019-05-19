<?php

namespace Dywee\OrderBundle\Service;

use Doctrine\ORM\EntityManager;
use Dywee\AddressBundle\Entity\CountryInterface;
use Dywee\OrderBundle\Entity\BaseOrder;
use Dywee\OrderBundle\Entity\BaseOrderInterface;
use Dywee\OrderBundle\Entity\Shipment;
use Dywee\OrderBundle\Entity\ShippingMethod as ShippingMethodEntity;
use Dywee\OrderBundle\Exception\ShipmentsNotCalculatedException;
use Dywee\OrderCMSBundle\Service\SessionOrderHandler;

class ShippingMethodHandler
{
    /** @var EntityManager */
    private $em;

    /** @var array */
    private $shipmentMethods = [];

    /** @var \Doctrine\ORM\EntityRepository|\Dywee\OrderBundle\Repository\ShippingMethodRepository */
    private $shipmentMethodRepository;

    /** @var SessionOrderHandler */
    private $orderSessionHandler;

    /**
     * ShippingMethodHandler constructor.
     *
     * @param EntityManager       $entityManager
     * @param SessionOrderHandler $sessionOrderHandler
     */
    public function __construct(EntityManager $entityManager, SessionOrderHandler $sessionOrderHandler)
    {
        $this->em = $entityManager;
        $this->shipmentMethodRepository = $this->em->getRepository(ShippingMethodEntity::class);
        $this->orderSessionHandler = $sessionOrderHandler;
    }

    /**
     * @param BaseOrder|null $order
     *
     * @return array
     * @throws \Exception
     */
    public function calculateForOrder(BaseOrder $order = null)
    {
        if (!$order) {
            $order = $this->orderSessionHandler->getOrderFromSession();
        }

        if (!$order->getShipments()) {
            throw new ShipmentsNotCalculatedException();
        }

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

    /**
     * @param Shipment         $shipment
     * @param CountryInterface $country
     *
     * @return array
     */
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

    /**
     * @param BaseOrderInterface $order
     */
    public function setNoShippingMethod(BaseOrderInterface $order)
    {
        $shippingMethodRepository = $this->em->getRepository(ShippingMethodEntity::class);
        $shippingMethod = $shippingMethodRepository->findOneByType('free');
        $order->setShippingMethod($shippingMethod);
    }
}