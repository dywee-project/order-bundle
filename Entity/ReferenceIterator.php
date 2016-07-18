<?php

namespace Dywee\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Dywee\AddressBundle\Entity\Country;

/**
 * ReferenceIterator
 *
 * @ORM\Table(name="reference_iterator")
 * @ORM\Entity(repositoryClass="Dywee\OrderBundle\Repository\ReferenceIteratorRepository")
 */
class ReferenceIterator
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="iteration", type="smallint")
     */
    private $iteration = 0;

    /**
     * @ORM\OneToOne(targetEntity="Dywee\AddressBundle\Entity\Country")
     */
    private $country;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set iteration
     *
     * @param integer $iteration
     *
     * @return ReferenceIterator
     */
    public function setIteration($iteration)
    {
        $this->iteration = $iteration;

        return $this;
    }

    /**
     * Get iteration
     *
     * @return int
     */
    public function getIteration()
    {
        return $this->iteration;
    }

    /**
     * @param Country $country
     * @return $this
     */
    public function setCountry(Country $country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }
}

