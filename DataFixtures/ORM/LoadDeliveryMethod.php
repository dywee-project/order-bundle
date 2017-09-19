<?php

namespace Dywee\OrderBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Dywee\OrderBundle\Entity\ShippingMethod;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class LoadDeliveryMethod extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $noShipping = new ShippingMethod();
        $noShipping->setName('No shipping');
        $noShipping->setActive(true);
        $noShipping->setType('free');

        $shop = new ShippingMethod();
        $shop->setName('Withdrawal in store');
        $shop->setActive(true);
        $shop->setDeliver($this->getReference('deliver-shop'));

        $mondialRelay = new ShippingMethod();
        $mondialRelay->setName('Mondial Relay');
        $mondialRelay->setActive(false);
        $mondialRelay->setDeliver($this->getReference('deliver-mondial-relay'));

        $manager->persist($noShipping);
        $manager->persist($shop);
        $manager->persist($mondialRelay);

        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }
}