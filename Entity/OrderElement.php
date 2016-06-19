<?php

namespace Dywee\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Dywee\ProductBundle\Entity\Product;

/**
 * OrderElement
 *
 * @ORM\Table(name="order_elements")
 * @ORM\Entity(repositoryClass="Dywee\OrderBundle\Repository\OrderElementRepository")
 */
class OrderElement
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
     * @ORM\ManyToOne(targetEntity="Dywee\OrderBundle\Entity\BaseOrder", inversedBy="orderElements")
     */
    private $order;

    /**
     * @ORM\ManyToOne(targetEntity="Dywee\ProductBundle\Entity\Product")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="smallint")
     */
    private $quantity = 1;

    private $oldQuantity;

    /**
     * @var float
     *
     * @ORM\Column(name="unitPrice", type="float")
     */
    private $unitPrice = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="TotalPrice", type="float")
     */
    private $totalPrice = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="locationCoeff", type="float")
     */
    private $locationCoeff = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="duration", type="smallint")
     */
    private $duration = 1;

    /**
     * @var float
     *
     * @ORM\Column(name="discountRate", type="float")
     */
    private $discountRate = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="discountValue", type="float")
     */
    private $discountValue = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="beginAt", type="datetime", nullable=true)
     */
    private $beginAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="endAt", type="datetime", nullable=true)
     */
    private $endAt;

    /**
     * @ORM\Column(name="wording", type="string", length=255, nullable=true)
     */
    private $wording;

    public function __construct()
    {

    }

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
     * @return OrderElement
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        $this->calculateTotalPrice();
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
     * Set unitPrice
     *
     * @param float $unitPrice
     * @return OrderElement
     */
    public function setUnitPrice($unitPrice)
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    /**
     * Get unitPrice
     *
     * @return float 
     */
    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

    /**
     * Set totalPrice
     *
     * @param float $totalPrice
     * @return OrderElement
     */
    public function setTotalPrice($totalPrice)
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    /**
     * Get totalPrice
     *
     * @return float 
     */
    public function getTotalPrice()
    {
        return $this->totalPrice;
    }

    /**
     * Set order
     *
     * @param \Dywee\OrderBundle\Entity\BaseOrder $order
     * @return OrderElement
     */
    public function setOrder(\Dywee\OrderBundle\Entity\BaseOrder $order = null)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return \Dywee\OrderBundle\Entity\BaseOrder 
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set product
     *
     * @param \Dywee\EshopBundle\Entity\Product $product
     * @return OrderElement
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;

        $price = $product->getIsPromotion() === true ? $product->getPromotionPrice() : $product->getPrice();

        $this->setUnitPrice($price);
        $this->calculateTotalPrice();
        return $this;
    }

    public function calculateTotalPrice()
    {
        $this->setTotalPrice($this->getUnitPrice()*$this->getQuantity()*$this->getLocationCoeff());
        return $this;
    }

    /**
     * Get product
     *
     * @return \Dywee\EshopBundle\Entity\Product 
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set locationCoeff
     *
     * @param integer $locationCoeff
     * @return OrderElement
     */
    public function setLocationCoeff($locationCoeff)
    {
        $this->locationCoeff = $locationCoeff;
        $this->calculateTotalPrice();
        return $this;
    }

    /**
     * Get locationCoeff
     *
     * @return integer 
     */
    public function getLocationCoeff()
    {
        return $this->locationCoeff;
    }

    /**
     * Set discountRate
     *
     * @param float $discountRate
     * @return OrderElement
     */
    public function setDiscountRate($discountRate, $fromValue = false)
    {
        $this->discountRate = $discountRate;

        if($this->getTotalPrice() > 0 && $fromValue == false && $discountRate > 0)
        {
            $this->setDiscountValue(($this->getTotalPrice()/$this->discountRate), true);
        }

        return $this;
    }

    /**
     * Get discountRate
     *
     * @return float 
     */
    public function getDiscountRate()
    {
        return $this->discountRate;
    }

    /**
     * Set discountValue
     *
     * @param float $discountValue
     * @return OrderElement
     */
    public function setDiscountValue($discountValue, $fromRate = false)
    {
        $this->discountValue = $discountValue;

        if($this->getTotalPrice() > 0)
        {
            if($fromRate == false)
                $this->setDiscountRate(($this->discountValue/$this->getTotalPrice()), true);

            $this->setTotalPrice(($this->getUnitPrice()*$this->getQuantity()*$this->getLocationCoeff()) - $this->discountRate);
        }

        return $this;
    }

    /**
     * Get discountValue
     *
     * @return float 
     */
    public function getDiscountValue()
    {
        return $this->discountValue;
    }

    /**
     * Set duration
     *
     * @param integer $duration
     * @return OrderElement
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        $data = array(
            0 => 1,
            1 => 1,
            2 => 1.9,
            3 => 2.7,
            4 => 3.2,
            5 => 3.6,
            6 => 3.8,
            7 => 4,
            8 => 4.5,
            9 => 5,
            10 => 5.5,
            11 => 6,
            12 => 6.5,
            13 => 7,
            14 => 7.5,
            15 => 7.9,
            16 => 8.3,
            17 => 8.7,
            18 => 9.1,
            19 => 9.5,
            20 => 9.9,
            21 => 10.3,
            22 => 10.6,
            23 => 10.9,
            24 => 11.2,
            25 => 11.5,
            26 => 11.8,
            27 => 12.1,
            28 => 12.4,
            29 => 12.7,
            30 => 13,
        );

        return $this->setLocationCoeff($data[$duration]);
    }

    /**
     * Get duration
     *
     * @return integer 
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set beginAt
     *
     * @param \dateTime $beginAt
     * @return OrderElement
     */
    public function setBeginAt(\dateTime $beginAt = null)
    {
        $this->beginAt = $beginAt;
        $this->timeInterval();

        return $this;
    }

    /**
     * Get beginAt
     *
     * @return \dateTime 
     */
    public function getBeginAt()
    {
        return $this->beginAt;
    }

    /**
     * Set endAt
     *
     * @param \dateTime $endAt
     * @return OrderElement
     */
    public function setEndAt(\dateTime $endAt)
    {
        $this->endAt = $endAt;
        $this->timeInterval();

        return $this;
    }

    /**
     * Get endAt
     *
     * @return \dateTime 
     */
    public function getEndAt()
    {
        return $this->endAt;
    }

    public function timeInterval()
    {
        if ($this->getBeginAt() != null && $this->getEndAt() != null) {
            $i = (int) $this->getBeginAt()->diff($this->getEndAt(), true)->format('%a');
            $this->setDuration($i++);
        }
    }

    /**
     * Get wording
     *
     * @return string
     */
    public function getWording()
    {
        return $this->wording;
    }

    /**
     * Set totalPrice
     *
     * @param string $wording
     * @return OrderElement
     */
    public function setWording($wording)
    {
        $this->wording = $wording;

        return $this;
    }
}