<?php

namespace Dywee\OrderBundle\DoctrineListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityManager;
use Dywee\OrderBundle\Entity\BaseOrder;

class StockManager
{
    public function preUpdate(LifecycleEventArgs $args)
    {
        return $this->manageStock($args);
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        return $this->manageStock($args);
    }

    public function manageStock($args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof BaseOrder) {
            return;
        }

        $em = $args->getEntityManager();

        //On va voir si la gestion des stocks est activée
        $pm = $em->getRepository('DyweeCoreBundle:ParametersManager');
        $stockEnabled = $pm->findOneByName('stockManagementEnabled');

        //Si le stock est activé on gère le stock
        if($stockEnabled->getValue() == 1)
        {
            //Si la commande est nouvelle et que le state est défini comme en attente, actif ou terminé on décrémente le stock
            if($entity->getId() == null && $entity->getState() >= 1)
                $entity->decreaseStock();

            if(is_numeric($entity->getOldState()))
                if($entity->getState() >= 1 && $entity->getOldState() < 1)
                    $entity->decreaseStock();
                elseif($entity->getState() < 1 && $entity->getOldState() >= 1)
                    $entity->refundStock();
        }

    }
}