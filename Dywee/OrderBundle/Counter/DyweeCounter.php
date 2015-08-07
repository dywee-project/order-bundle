<?php
/**
 * Created by PhpStorm.
 * User: Olivier
 * Date: 15/02/15
 * Time: 14:10
 */

namespace Dywee\OrderBundle\Counter;

use Doctrine\ORM\EntityManager;
use Dywee\CoreBundle\Entity\ParametersManager;

class DyweeCounter{

    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getNextOfferReference()
    {
        $pr = $this->em->getRepository('DyweeCoreBundle:ParametersManager');
        $parameter = $pr->findOneByName('offerIterator');

        if($parameter == null)
        {
            $parameter = new ParametersManager();
            $parameter->setName('offerIterator');
            $parameter->setValue(1);
            $value = 1;
        }
        else
        {
            $value = (int) $parameter->getValue();
            $value++;
            $parameter->setValue($value);
        }

        $this->em->persist($parameter);
        $this->em->flush();

        $finalValue = $value;

        while(strlen($finalValue) < 4)
            $finalValue = '0'.$finalValue;

        $year = date("Y");

        return $year.'/'.$finalValue;
    }

    public function getNextOrderReference($order)
    {
        $country = $order->getShippingAddress()->getCountry();
        $parameterName = $country->getIso().'OrderIterator';
        $pr = $this->em->getRepository('DyweeCoreBundle:ParametersManager');
        $parameter = $pr->findOneByName('offerIterator');

        if($parameter == null)
        {
            $parameter = new ParametersManager();
            $parameter->setName($parameterName);
            $parameter->setValue(1);
            $value = 1;
        }
        else
        {
            $value = (int) $parameter->getValue();
            $value++;
            $parameter->setValue($value);
        }

        $this->em->persist($parameter);
        $this->em->flush();

        $finalValue = $value;

        while(strlen($finalValue) < 4)
            $finalValue = '0'.$finalValue;

        return $country->getIso().' '.$finalValue;
    }
}