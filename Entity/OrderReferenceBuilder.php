<?php

namespace Dywee\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderReferenceBuilder
 *
 * @ORM\Table(name="order_reference_builder")
 * @ORM\Entity(repositoryClass="Dywee\OrderBundle\Repository\OrderReferenceBuilderRepository")
 */
class OrderReferenceBuilder
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
     * @var string
     *
     * @ORM\Column(name="prefix", type="string", length=255)
     */
    private $prefix = '[country]';

    /**
     * @var string
     *
     * @ORM\Column(name="iteration", type="string", length=255)
     */
    private $iteration = 1;

    /**
     * @var string
     *
     * @ORM\Column(name="suffix", type="string", length=255)
     */
    private $suffix = '';

    /**
     * @ORM\Column(type="boolean")
     */
    private $byCountry = false;

    /**
     * @ORM\Column(type="smallint")
     */
    private $digitNumber = 5;


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
     * Set prefix
     *
     * @param string $prefix
     *
     * @return OrderReferenceBuilder
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * Get prefix
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Set iteration
     *
     * @param string $iteration
     *
     * @return OrderReferenceBuilder
     */
    public function setIteration($iteration)
    {
        $this->iteration = $iteration;

        return $this;
    }

    /**
     * Get iteration
     *
     * @return string
     */
    public function getIteration()
    {
        return $this->iteration;
    }

    /**
     * Set suffix
     *
     * @param string $suffix
     *
     * @return OrderReferenceBuilder
     */
    public function setSuffix($suffix)
    {
        $this->suffix = $suffix;

        return $this;
    }

    /**
     * Get suffix
     *
     * @return string
     */
    public function getSuffix()
    {
        return $this->suffix;
    }

    /**
     * @return boolean
     */
    public function getByCountry()
    {
        return $this->byCountry;
    }

    /**
     * @param boolean $byCountry
     * @return $this
     */
    public function setByCountry($byCountry)
    {
        $this->byCountry = $byCountry;

        return $this;
    }

    /**
     * @param integer $number
     * @return $this
     */
    public function setDigitNumber($number)
    {
        $this->digitNumber = $number;

        return $this;
    }

    /**
     * @return integer
     */
    public function getDigitNumber()
    {
        return $this->digitNumber;
    }
}

