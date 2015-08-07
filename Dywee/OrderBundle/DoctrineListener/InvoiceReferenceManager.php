<?php
/**
 * Created by PhpStorm.
 * User: Olivier
 * Date: 16/02/15
 * Time: 17:05
 */

namespace Dywee\OrderBundle\DoctrineListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityManager;
use Dywee\CoreBundle\Entity\ParametersManager;
use Dywee\OrderBundle\Entity\BaseOrder;
use Dywee\ProductBundle\Entity\ProductStat;

class InvoiceReferenceManager {

    public function preUpdate(LifecycleEventArgs $args)
    {
        return $this->invoice($args);
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        return $this->invoice($args);
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        return $this->incrementInvoice($args);
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        return $this->incrementInvoice($args);
    }

    public function incrementInvoice($args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();

        // On veut que les entités BaseOrder
        if (!$entity instanceof BaseOrder) {
            return;
        }
        if($entity->getState() >= 2 && $entity->getShippingAddress() != null && $entity->getShippingAddress()->getCountry() != null) {

            $parameterName = $entity->getShippingAddress()->getCountry()->getIso().'OrderIterator';
            $pr = $em->getRepository('DyweeCoreBundle:ParametersManager');
            $parameter = $pr->findOneByName($parameterName);

            if ($parameter == null) {
                $parameter = new ParametersManager();
                $parameter->setName($parameterName);
                $value = 1;
            } else {
                $value = (int)$parameter->getValue();
                $value++;
            }

            $parameter->setValue($value);


            //Stat d'achats des produits
            $productStat = new ProductStat();

            $productStat->setType(3);

            foreach($entity->getOrderElements() as $orderElement)
            {
                $productStat->setProduct($orderElement->getProduct());
                $productStat->setQuantity($orderElement->getQuantity());
            }

            $em->persist($parameter);
            $em->persist($productStat);
            $em->flush();
        }
    }

    public function invoice(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();

        // On veut que les entités BaseOrder
        if (!$entity instanceof BaseOrder) {
            return;
        }

        if($entity->getState() >= 2 && $entity->getShippingAddress() != null && $entity->getShippingAddress()->getCountry() != null && $entity->getInvoiceReference() == null)
        {
            $country = $entity->getShippingAddress()->getCountry();
            $parameterName = $country->getIso().'OrderIterator';
            $pr = $em->getRepository('DyweeCoreBundle:ParametersManager');
            $parameter = $pr->findOneByName($parameterName);

            if($parameter == null)
                $value = 1;
            else
            {
                $value = (int) $parameter->getValue();
                $value++;
            }

            $finalValue = $value;

            while(strlen($finalValue) < 4)
                $finalValue = '0'.$finalValue;

            $entity->setInvoiceReference($country->getIso().' '.$finalValue);

            return $entity;
        }
    }
}