<?php

namespace AppBundle\DataFixtures\ORM;

use CompositionBundle\Entity\MusicKey;
use CompositionBundle\Entity\TimeSignature;
use CompositionBundle\Entity\Track;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Dywee\OrderBundle\Entity\Deliver;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class LoadDeliver extends AbstractFixture
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
}