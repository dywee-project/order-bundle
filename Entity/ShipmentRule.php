<?php

namespace Dywee\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ShipmentRule
 *
 * @ORM\Table(name="shipment_rule")
 * @ORM\Entity(repositoryClass="Dywee\OrderBundle\Repository\ShipmentRuleRepository")
 */
class ShipmentRule
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="mappedKey", type="string", length=255)
     */
    private $mappedKey;

    /**
     * @var string
     *
     * @ORM\Column(name="operator", type="string", length=3)
     */
    private $operator;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=255)
     */
    private $value;


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
     * Set name
     *
     * @param string $name
     *
     * @return ShipmentRule
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return ShipmentRule
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set mappedKey
     *
     * @param string $mappedKey
     *
     * @return ShipmentRule
     */
    public function setMappedKey($mappedKey)
    {
        $this->mappedKey = $mappedKey;

        return $this;
    }

    /**
     * Get mappedKey
     *
     * @return string
     */
    public function getMappedKey()
    {
        return $this->mappedKey;
    }

    /**
     * Set operator
     *
     * @param string $operator
     *
     * @return ShipmentRule
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;

        return $this;
    }

    /**
     * Get operator
     *
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return ShipmentRule
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

}

