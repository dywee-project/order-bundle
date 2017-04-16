<?php

namespace Dywee\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Dywee\OrderBundle\Entity\Shipment;
use Dywee\ProductBundle\Entity\BaseProduct;

/**
 * ShipmentElement
 *
 * @ORM\Table(name="shipment_elements")
 * @ORM\Entity(repositoryClass="Dywee\OrderBundle\Repository\ShipmentElementRepository")
 */
class ShipmentElement
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="smallint")
     */
    private $quantity;

    /**
     * @ORM\ManyToOne(targetEntity="Dywee\OrderBundle\Entity\Shipment", inversedBy="shipmentElements", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $shipment;

    /**
     * @ORM\ManyToOne(targetEntity="Dywee\ProductBundle\Entity\BaseProduct")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @ORM\Column(name="weight", type="float")
     */
    private $weight = 0;

    /**
     * @var int
     * @ORM\Column(type="boolean")
     */
    private $canBeReconciliated = true;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     * @return ShipmentElement
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer 
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set shipment
     *
     * @param Shipment $shipment
     * @return ShipmentElement
     */
    public function setShipment(Shipment $shipment)
    {
        $this->shipment = $shipment;

        return $this;
    }

    /**
     * Get shipment
     *
     * @return Shipment
     */
    public function getShipment()
    {
        return $this->shipment;
    }

    /**
     * Set product
     *
     * @param BaseProduct $product
     * @return ShipmentElement
     */
    public function setProduct(BaseProduct $product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return \Dywee\ProductBundle\Entity\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set weight
     *
     * @param float $weight
     * @return ShipmentElement
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight
     *
     * @return float 
     */
    public function getWeight()
    {
        return $this->weight;
    }

    public function canBeReconciliated()
    {
        return $this->canBeReconciliated;
    }

    /**
     * @return int
     */
    public function getCanBeReconciliated()
    {
        return $this->canBeReconciliated;
    }

    /**
     * @param int $canBeReconciliated
     * @return ShipmentElement
     */
    public function setCanBeReconciliated($canBeReconciliated)
    {
        $this->canBeReconciliated = $canBeReconciliated;
        return $this;
    }
}
