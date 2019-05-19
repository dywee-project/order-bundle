<?php

namespace Dywee\OrderBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Dywee\ProductBundle\Entity\BaseProduct;
use Dywee\ProductBundle\Entity\Product;

/**
 * OrderElement
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Dywee\OrderBundle\Repository\OrderDiscountRepository")
 */
class OrderDiscount
{

    const COMBINABLE_TRUE = 'disabled';
    const COMBINABLE_ONLY_SELF = 'self';
    const COMBINABLE_EXCLUDING_SELF = 'other';
    const COMBINABLE_FALSE = 'enabled';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=10)
     */
    private $combinable = self::COMBINABLE_EXCLUDING_SELF;


    /**
     * @var float
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $price;

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $discount;

    /**
     * @var \Datetime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $beginAt;

    /**
     * @var \Datetime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endAt;

    /**
     * @ORM\OneToMany(targetEntity="OrderDiscountElement", mappedBy="discount")
     */
    private $elements;

    public function __construct()
    {
        $this->elements = new ArrayCollection();
    }


    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCombinable()
    {
        return $this->combinable;
    }

    /**
     * @param string $combinable
     *
     * @return OrderElement
     */
    public function setCombinable($combinable)
    {
        $this->combinable = $combinable;

        return $this;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float $price
     *
     * @return OrderElement
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return float
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @param float $discount
     *
     * @return OrderElement
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * @return \Datetime
     */
    public function getBeginAt()
    {
        return $this->beginAt;
    }

    /**
     * @param \Datetime $beginAt
     *
     * @return OrderElement
     */
    public function setBeginAt($beginAt)
    {
        $this->beginAt = $beginAt;

        return $this;
    }

    /**
     * @return \Datetime
     */
    public function getEndAt()
    {
        return $this->endAt;
    }

    /**
     * @param \Datetime $endAt
     *
     * @return OrderElement
     */
    public function setEndAt($endAt)
    {
        $this->endAt = $endAt;

        return $this;
    }


    public function addDiscountElement(OrderDiscountElement $element)
    {
        $this->elements[] = $element;
        $element->setDiscount($this);

        return $this;
    }

    public function getDiscountElements()
    {
        return $this->elements;
    }

    public function removeDiscountElement(OrderDiscountElement $element)
    {
        return $this->elements->removeElement($element);
    }
}
