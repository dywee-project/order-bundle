<?php

namespace Dywee\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Offer
 *
 * @ORM\Table(name="offers")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 *
 */
class Offer
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
     * @var float
     *
     * @ORM\Column(name="priceVatExcl", type="float")
     */
    private $priceVatExcl = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="deliveryCost", type="float")
     */
    private $deliveryCost = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="vatRate", type="float")
     */
    private $vatRate = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="vatPrice", type="float")
     */
    private $vatPrice = 0;

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
     * @var float
     *
     * @ORM\Column(name="priceVatIncl", type="float")
     */
    private $priceVatIncl = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="totalPrice", type="float")
     */
    private $totalPrice = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description = '';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="validatedAt", type="datetime", nullable=true)
     */
    private $validatedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="reference", type="string", length=255)
     */
    private $reference = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="state", type="smallint")
     */
    private $state = 1;

    /**
     * @ORM\ManyToOne(targetEntity="Dywee\AddressBundle\Entity\Address", cascade={"persist"})
     */
    private $address;

    /**
     * @ORM\OneToMany(targetEntity="Dywee\OrderBundle\Entity\OfferElement", mappedBy="offer", cascade={"persist", "remove"})
     */
    private $offerElements;

    /**
     * @ORM\Column(name="isPriceTTC", type="boolean")
     */
    private $isPriceTTC = true;

    /**
     * @ORM\ManyToOne(targetEntity="Dywee\WebsiteBundle\Entity\Website")
     */
    private $website;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="Dywee\ShipmentBundle\Entity\Deliver")
     * @ORM\JoinColumn(nullable=true)
     */
    private $deliver;

    /**
     * @var string
     *
     * @ORM\Column(name="deliveryMethod", type="string", length=255, nullable=true)
     */
    private $deliveryMethod;

    /**
     * @var string
     *
     * @ORM\Column(name="deliveryInfo", type="string", length=255, nullable=true)
     */
    private $deliveryInfo;

    /**
     * @ORM\ManyToOne(targetEntity="Dywee\UserBundle\Entity\User")
     */
    private $createdBy;

    /**
     * @var integer
     *
     * @ORM\Column(name="duration", type="smallint")
     */
    private $duration = 0;

    /**
     * @ORM\Column(name="sellType", type="smallint", nullable=true)
     */
    private $sellType;

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
     * Set priceVatExcl
     *
     * @param float $priceVatExcl
     * @return Offer
     */
    public function setPriceVatExcl($priceVatExcl)
    {
        $this->priceVatExcl = $priceVatExcl;

        return $this;
    }

    /**
     * Get priceVatExcl
     *
     * @return float 
     */
    public function getPriceVatExcl()
    {
        return $this->priceVatExcl;
    }

    /**
     * Set deliveryCost
     *
     * @param float $deliveryCost
     * @return Offer
     */
    public function setDeliveryCost($deliveryCost)
    {
        $this->deliveryCost = $deliveryCost;

        return $this;
    }

    /**
     * Get deliveryCost
     *
     * @return float 
     */
    public function getDeliveryCost()
    {
        return $this->deliveryCost;
    }

    /**
     * Set vatRate
     *
     * @param float $vatRate
     * @return Offer
     */
    public function setVatRate($vatRate)
    {
        $this->vatRate = $vatRate;

        return $this;
    }

    /**
     * Get vatRate
     *
     * @return float 
     */
    public function getVatRate()
    {
        return $this->vatRate;
    }

    /**
     * Set vatPrice
     *
     * @param float $vatPrice
     * @return Offer
     */
    public function setVatPrice($vatPrice)
    {
        $this->vatPrice = $vatPrice;

        return $this;
    }

    /**
     * Get vatPrice
     *
     * @return float 
     */
    public function getVatPrice()
    {
        return $this->vatPrice;
    }

    /**
     * Set discountRate
     *
     * @param float $discountRate
     * @return Offer
     */
    public function setDiscountRate($discountRate)
    {
        $this->discountRate = $discountRate;

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
     * @return Offer
     */
    public function setDiscountValue($discountValue)
    {
        $this->discountValue = $discountValue;

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
     * Set priceVatIncl
     *
     * @param float $priceVatIncl
     * @return Offer
     */
    public function setPriceVatIncl($priceVatIncl)
    {
        $this->priceVatIncl = $priceVatIncl;

        return $this;
    }

    /**
     * Get priceVatIncl
     *
     * @return float 
     */
    public function getPriceVatIncl()
    {
        return $this->priceVatIncl;
    }

    /**
     * Set totalPrice
     *
     * @param float $totalPrice
     * @return Offer
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
     * Set description
     *
     * @param string $description
     * @return Offer
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Offer
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set validatedAt
     *
     * @param \DateTime $validatedAt
     * @return Offer
     */
    public function setValidatedAt($validatedAt)
    {
        $this->validatedAt = $validatedAt;

        return $this;
    }

    /**
     * Get validatedAt
     *
     * @return \DateTime 
     */
    public function getValidatedAt()
    {
        return $this->validatedAt;
    }

    /**
     * Set reference
     *
     * @param string $reference
     * @return Offer
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get reference
     *
     * @return string 
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set state
     *
     * @param integer $state
     * @return Offer
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return integer 
     */
    public function getState()
    {
        return $this->state;
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->offerElements = new \Doctrine\Common\Collections\ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->state = 1;
    }

    /**
     * Set address
     *
     * @param \Dywee\AddressBundle\Entity\Address $address
     * @return Offer
     */
    public function setAddress(\Dywee\AddressBundle\Entity\Address $address = null)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return \Dywee\AddressBundle\Entity\Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Add offerElements
     *
     * @param \Dywee\OrderBundle\Entity\OfferElement $offerElements
     * @return Offer
     */
    public function addOfferElement(\Dywee\OrderBundle\Entity\OfferElement $offerElements)
    {
        $this->offerElements[] = $offerElements;
        $offerElements->setOffer($this);
        return $this;
    }

    /**
     * Remove offerElements
     *
     * @param \Dywee\OrderBundle\Entity\OfferElement $offerElements
     */
    public function removeOfferElement(\Dywee\OrderBundle\Entity\OfferElement $offerElements)
    {
        $this->offerElements->removeElement($offerElements);
    }

    /**
     * Get offerElements
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOfferElements()
    {
        return $this->offerElements;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function forcePriceCalculation()
    {
        $isTTC = true;
        $price = 0;
        $this->setVatRate($this->getAddress()->getCountry()->getVatRate());
        $this->setPriceVatIncl(0);
        foreach($this->getOfferElements() as $offerElement)
            $price += $offerElement->getTotalPrice();


        if($isTTC)
        {
            $this->setPriceVatIncl($price);
            $this->setPriceVatExcl($price/(1 + $this->getVatRate()/100));
            $this->setVatPrice($this->getPriceVatIncl()-$this->getPriceVatExcl());
        }
        else{
            /*$this->setPriceVatExcl($price);
            $this->setVatPrice($this->getPriceVatExcl()*$this->getVatRate()/100);
            $this->setPriceVatIncl($this->getPriceVatExcl()+$this->getVatPrice());*/
        }

        if($this->getDiscountRate() > 0 || $this->getDiscountValue() > 0)
        {
            if($this->getDiscountValue() == 0)
                $this->setDiscountValue($this->getDiscountRate()*$this->getPriceVatExcl()/100);
            else if($this->getDiscountRate() == 0)
                $this->setDiscountRate(100*$this->getDiscountValue()/$this->getPriceVatExcl());
        }

        $this->setTotalPrice($this->getPriceVatIncl() + $this->getDeliveryCost() - $this->getDiscountValue());

        return $this;
    }

    /**
     * Set deliveryMethod
     *
     * @param string $deliveryMethod
     * @return Offer
     */
    public function setDeliveryMethod($deliveryMethod)
    {
        $this->deliveryMethod = $deliveryMethod;

        return $this;
    }

    /**
     * Get deliveryMethod
     *
     * @return string 
     */
    public function getDeliveryMethod()
    {
        return $this->deliveryMethod;
    }

    /**
     * Set deliveryInfo
     *
     * @param string $deliveryInfo
     * @return Offer
     */
    public function setDeliveryInfo($deliveryInfo)
    {
        $this->deliveryInfo = $deliveryInfo;

        return $this;
    }

    /**
     * Get deliveryInfo
     *
     * @return string 
     */
    public function getDeliveryInfo()
    {
        return $this->deliveryInfo;
    }

    /**
     * Set deliver
     *
     * @param \Dywee\ShipmentBundle\Entity\Deliver $deliver
     * @return Offer
     */
    public function setDeliver(\Dywee\ShipmentBundle\Entity\Deliver $deliver = null)
    {
        $this->deliver = $deliver;

        return $this;
    }

    /**
     * Get deliver
     *
     * @return \Dywee\ShipmentBundle\Entity\Deliver 
     */
    public function getDeliver()
    {
        return $this->deliver;
    }

    public function containsElementReduction()
    {
        foreach($this->offerElements as $offerElement)
            if($offerElement->getDiscountValue() > 0)return true;
        return false;
    }

    /**
     * Set createdBy
     *
     * @param \Dywee\UserBundle\Entity\User $createdBy
     * @return Offer
     */
    public function setCreatedBy(\Dywee\UserBundle\Entity\User $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \Dywee\UserBundle\Entity\User 
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set duration
     *
     * @param integer $duration
     * @return Offer
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
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
     * Set isPriceTTC
     *
     * @param boolean $isPriceTTC
     *
     * @return Offer
     */
    public function setIsPriceTTC($isPriceTTC)
    {
        $this->isPriceTTC = $isPriceTTC;

        return $this;
    }

    /**
     * Get isPriceTTC
     *
     * @return boolean
     */
    public function getIsPriceTTC()
    {
        return $this->isPriceTTC;
    }

    /**
     * Set sellType
     *
     * @param integer $sellType
     *
     * @return Offer
     */
    public function setSellType($sellType)
    {
        $this->sellType = $sellType;

        return $this;
    }

    /**
     * Get sellType
     *
     * @return integer
     */
    public function getSellType()
    {
        return $this->sellType;
    }

    /**
     * Set website
     *
     * @param \Dywee\WebsiteBundle\Entity\Website $website
     * @return Offer
     */
    public function setWebsite(\Dywee\WebsiteBundle\Entity\Website $website = null)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get website
     *
     * @return \Dywee\WebsiteBundle\Entity\Website 
     */
    public function getWebsite()
    {
        return $this->website;
    }
}
