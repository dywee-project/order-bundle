<?php
namespace Dywee\OrderBundle\Service;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\NoResultException;
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

    protected function invoice($args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();

        if (!$entity instanceof BaseOrder) {
            return;
        }

        if($entity->isElligibleForInvoice()) {
            $country = $entity->getShippingAddress()->getCountry();

            $orderReferenceBuilderRepository = $em->getRepository('DyweeOrderBundle:OrderReferenceBuilder');
            $orderReferenceBuilder = $orderReferenceBuilderRepository->findOneById(1);

            // Create default builder if not existing
            if (!$orderReferenceBuilder) {
                $orderReferenceBuilder = new OrderReferenceBuilder();
                $em->persist($orderReferenceBuilder);
            }

            // Add reference prefix
            $reference = $orderReferenceBuilder->getPrefix();

            // Add country reference if needed and if prefix does'nt already added it
            if ($orderReferenceBuilder->getByCountry()) {
                if ($orderReferenceBuilder->getPrefix() !== '[country]') {
                    if (strlen($reference) > 0) {
                        $reference .= ' ';
                    }
                    $reference .= $country->getIso();
                }
                else{
                    $reference = str_replace(array('[country]'), array($country->getIso()), $reference);
                }
            }
            else{
                $country = null;
            }

            // Add iteration
            $iteratorRepository = $em->getRepository('DyweeOrderBundle:ReferenceIterator');
            $iterator = $iteratorRepository->findOneByCountry($country);

            if(!$iterator)
            {
                $iterator = new ReferenceIterator();
                $iterator->setCountry($country);
            }

            $iteration = $iterator->getIteration();

            // Conform to digit number
            while(strlen($iteration) < $orderReferenceBuilder->getDigitNumber())
                $iteration = '0'.$iteration;

            $reference .= $iteration;

            // Add reference suffix
            $reference .= $orderReferenceBuilder->getSuffix();

            $entity->setInvoiceReference($reference);
            $entity->justGotInvoice = true;
        }

    }

    protected function incrementInvoice($args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();

        if (!$entity instanceof BaseOrder){
            return;
        }

        if(!$entity->justGotInvoice)
            return;

        $country = $entity->getShippingAddress()->getCountry();

        $orderReferenceBuilderRepository = $em->getRepository('DyweeOrderBundle:OrderReferenceBuilder');
        $orderReferenceBuilder = $orderReferenceBuilderRepository->findOneById(1);

        if (!$orderReferenceBuilder->getByCountry()) {
            $country = null;
        }

        // Add iteration
        $iteratorRepository = $em->getRepository('DyweeOrderBundle:ReferenceIterator');
        $iterator = $iteratorRepository->findOneByCountry($country);

        if(!$iterator)
        {
            $iterator = new ReferenceIterator();
            $iterator->setCountry($country);
        }

        $iterator->iterate();

        $em->persist($iterator);
        $em->flush();

        $entity->justGotInvoice = false;
    }
}