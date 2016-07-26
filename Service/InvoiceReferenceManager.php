<?php
namespace Dywee\OrderBundle\Service;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Dywee\OrderBundle\Entity\BaseOrder;
use Dywee\OrderBundle\Entity\OrderReferenceBuilder;
use Dywee\OrderBundle\Entity\ReferenceIterator;
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

        if (!$entity instanceof BaseOrder) {
            return;
        }

        //TODO réécrire la fonction de check de référence
        if($entity->getState() >= 2 && $entity->getShippingAddress() != null && $entity->getShippingAddress()->getCountry() != null) {
            $orderReferenceBuilderRepository = $em->getRepository('DyweeOrderBundle:OrderReferenceBuilder');
            $orderReferenceBuilder = $orderReferenceBuilderRepository->findById(1);

            // Create default builder if not existing
            if(!$orderReferenceBuilder)
            {
                $orderReferenceBuilder = new OrderReferenceBuilder();
                $em->persist($orderReferenceBuilder);
            }

            // Add iteration
            $iteratorRepository = $em->getRepository('DyweeOrderBundle:ReferenceIterator');
            $iterator = $iteratorRepository->findItertor($orderReferenceBuilder, $entity->getShippingAddress()->getCountry()->getIso());
            $iterator = (int) $iterator->getIteration()+1;

            //Stat d'achats des produits
            $productStat = new ProductStat();
            $productStat->setType(3);

            foreach($entity->getOrderElements() as $orderElement)
            {
                $productStat->setProduct($orderElement->getProduct());
                $productStat->setQuantity($orderElement->getQuantity());
            }

            $em->persist($iterator);
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
            $orderReferenceBuilderRepository = $em->getRepository('DyweeOrderBundle:OrderReferenceBuilder');
            $orderReferenceBuilder = $orderReferenceBuilderRepository->findById(1);

            // Create default builder if not existing
            if(!$orderReferenceBuilder)
            {
                $orderReferenceBuilder = new OrderReferenceBuilder();
                $em->persist($orderReferenceBuilder);
            }
            $reference = '';

            // Add country reference if needed
            if($orderReferenceBuilder->getByCountry())
                $reference = $entity->getShippingAddress()->getCountry()->getIso();

            // TODO Add prefix
            // Add iteration
            $iteratorRepository = $em->getRepository('DyweeOrderBundle:ReferenceIterator');
            $iterator = $iteratorRepository->findItertor($orderReferenceBuilder, $entity->getShippingAddress()->getCountry()->getIso());

            if(!$iterator)
            {
                $iterator = new ReferenceIterator();
                if($orderReferenceBuilder->getByCountry())
                    $iterator->setCountry($entity->getShippingAddress()->getCountry()->getIso());
                $em->persist($iterator);
            }
            else
                $iterator = $iterator[0];

            $iteration = (int) $iterator->getIteration()+1;

            while(strlen($iteration) < $orderReferenceBuilder->getDigitNumber())
                $iteration = '0'.$iteration;

            $reference .= $iteration;
            $entity->setInvoiceReference($reference);
        }
    }
}