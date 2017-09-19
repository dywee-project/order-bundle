<?php

namespace Dywee\OrderBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Dywee\OrderBundle\Entity\Deliver;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class LoadDeliver extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $shop = new Deliver();
        $shop->setName('Withdrawal in store');
        $shop->setActive(1);

        $mondialRelay = new Deliver();
        $mondialRelay->setName('Mondial Relay');

        $manager->persist($shop);
        $manager->persist($mondialRelay);

        $manager->flush();

        $this->addReference('deliver-shop', $shop);
        $this->addReference('deliver-mondial-relay', $mondialRelay);
    }

    public function getOrder()
    {
        return 1;
    }
}