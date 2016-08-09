<?php

namespace Dywee\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Dywee\ProductBundle\Entity\BaseProduct;
use Dywee\ProductBundle\Entity\Product;

/**
 * OrderElement
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Dywee\OrderBundle\Repository\OrderDiscountElementRepository")
 */
class OrderDiscountElement
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
     * @var int
     * @ORM\Column(type="smallint")
     */
    private $iterator;

    /**
     * @ORM\ManyToOne(targetEntity="OrderDiscount", inversedBy="discountElements")
     */
    private $discount;

    /**
     * @ORM\ManyToOne(targetEntity="BaseOrder", inversedBy="discountElements")
     */
    private $order;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @param mixed $discount
     * @return OrderDiscountElement
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param mixed $order
     * @return OrderDiscountElement
     */
    public function setOrder($order)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIterator()
    {
        return $this->iterator;
    }

    /**
     * @param mixed $iterator
     * @return OrderDiscountElement
     */
    public function setIterator($iterator)
    {
        $this->iterator = $iterator;
        return $this;
    }
}
