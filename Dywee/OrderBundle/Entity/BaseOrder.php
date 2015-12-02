<?php

namespace Dywee\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Dywee\ShipmentBundle\Entity\Shipment;
use Dywee\ShipmentBundle\Entity\ShipmentElement;

/**
 * BaseOrder
 *
 * @ORM\Table(name="orders")
 * @ORM\Entity(repositoryClass="Dywee\OrderBundle\Entity\BaseOrderRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class BaseOrder
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
     * @var boolean
     *
     * @ORM\Column(name="isGift", type="boolean")
     */
    private $isGift = 0;

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
     * @var string
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
     * @var string
     *
     * @ORM\Column(name="discountCode", type="string", length=255, nullable=true)
     */
    private $discountCode;

    /**
     * @var float
     *
     * @ORM\Column(name="discountRate", type="float", nullable=true)
     */
    private $discountRate;

    /**
     * @var float
     *
     * @ORM\Column(name="discountValue", type="float", nullable=true)
     */
    private $discountValue;

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
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creationDate", type="datetime")
     */
    private $creationDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="endingDate", type="datetime", nullable=true)
     */
    private $endingDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updateDate", type="datetime", nullable=true)
     */
    private $updateDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="validationDate", type="datetime", nullable=true)
     */
    private $validationDate;

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
     * @var integer
     *
     * @ORM\Column(name="deliveryState", type="smallint")
     */
    private $deliveryState = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="payementMethod", type="smallint", nullable=true)
     */
    private $payementMethod;

    /**
     * @var string
     *
     * @ORM\Column(name="payementInfos", type="string", length=255, nullable=true)
     */
    private $payementInfos;

    /**
     * @var string
     *
     * @ORM\Column(name="PayementState", type="string", length=255)
     */
    private $payementState = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="reference", type="string", length=255)
     */
    private $reference = '';

    /**
     * @var string
     *
     * @ORM\Column(name="invoiceReference", type="string", length=255, nullable=true)
     */
    private $invoiceReference;

    /**
     * @var string
     *
     * @ORM\Column(name="shippingMessage", type="text", nullable=true)
     */
    private $shippingMessage;

    /**
     * @var string
     *
     * @ORM\Column(name="locale", type="string", length=255, nullable=true)
     */
    private $locale;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="deposit", type="float")
     */
    private $deposit = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="state", type="smallint", nullable=true)
     */
    private $state = -1;

    /**
     * @ORM\ManyToOne(targetEntity="Dywee\UserBundle\Entity\User", cascade={"persist"})
     */
    private $billingUser;

    /**
     * @ORM\ManyToOne(targetEntity="Dywee\UserBundle\Entity\User", cascade={"persist"})
     */
    private $shippingUser;

    /**
     * @ORM\ManyToOne(targetEntity="Dywee\AddressBundle\Entity\Address", cascade={"persist"})
     */
    private $billingAddress;

    /**
     * @ORM\ManyToOne(targetEntity="Dywee\AddressBundle\Entity\Address", cascade={"persist"})
     */
    private $shippingAddress;

    /**
     * @ORM\OneToMany(targetEntity="Dywee\OrderBundle\Entity\OrderElement", mappedBy="order", cascade={"persist", "remove"})
     */
    private $orderElements;

    /**
     * @ORM\OneToMany(targetEntity="Dywee\ShipmentBundle\Entity\Shipment", mappedBy="order", cascade={"persist", "remove"})
     */
    private $shipments;

    /**
     * @ORM\ManyToOne(targetEntity="Dywee\ShipmentBundle\Entity\ShipmentMethod", cascade={"persist"})
     */
    private $shippingMethod;

    /**
     * @ORM\Column(name="weight", type="float")
     */
    private $weight = 0;

    /**
     * @ORM\Column(name="mailSended", type="boolean")
     */
    private $mailSended = false;

    /**
     * @ORM\Column(name="isPriceTTC", type="boolean")
     */
    private $isPriceTTC = true;

    /**
     * @ORM\Column(name="sellType", type="smallint", nullable=true)
     */
    private $sellType;

    /**
     * @ORM\Column(name="collectionAt", type="datetime", nullable=true)
     */
    private $collectionAt;

    /**
     * @ORM\Column(name="returnFor", type="datetime", nullable=true)
     */
    private $returnFor;

    /**
     * @ORM\Column(name="returnedAt", type="datetime", nullable=true)
     */
    private $returnedAt;

    /**
     * @ORM\ManyToOne(targetEntity="Dywee\WebsiteBundle\Entity\Website")
     */
    private $website;

    /**
     * @var integer
     *
     * @ORM\Column(name="duration", type="smallint")
     */
    private $duration = 0;

    private $mustRecaculShipments = false;

    private $oldState = null;

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
     * Set isGift
     *
     * @param boolean $isGift
     * @return BaseOrder
     */
    public function setIsGift($isGift)
    {
        $this->isGift = $isGift;

        return $this;
    }

    /**
     * Get isGift
     *
     * @return boolean
     */
    public function getIsGift()
    {
        return $this->isGift;
    }

    /**
     * Set priceVatExcl
     *
     * @param float $priceVatExcl
     * @return BaseOrder
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
     * @return BaseOrder
     */
    public function setDeliveryCost($deliveryCost)
    {
        $this->deliveryCost = $deliveryCost;

        return $this;
    }

    public function setShippingCost($deliveryCost)
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

    public function getShippingCost()
    {
        return $this->deliveryCost;
    }

    /**
     * Set vatRate
     *
     * @param string $vatRate
     * @return BaseOrder
     */
    public function setVatRate($vatRate)
    {
        $this->vatRate = $vatRate;

        return $this;
    }

    /**
     * Get vatRate
     *
     * @return string
     */
    public function getVatRate()
    {
        return $this->vatRate;
    }

    /**
     * Set vatPrice
     *
     * @param float $vatPrice
     * @return BaseOrder
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
     * Set discountCode
     *
     * @param string $discountCode
     * @return BaseOrder
     */
    public function setDiscountCode($discountCode)
    {
        $this->discountCode = $discountCode;

        return $this;
    }

    /**
     * Get discountCode
     *
     * @return string
     */
    public function getDiscountCode()
    {
        return $this->discountCode;
    }

    /**
     * Set discountRate
     *
     * @param float $discountRate
     * @return BaseOrder
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
     * Set priceVatIncl
     *
     * @param float $priceVatIncl
     * @return BaseOrder
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
     * @return BaseOrder
     */
    public function setTotalPrice($totalPrice)
    {
        $this->totalPrice = $totalPrice;
        if($this->totalPrice == 0)$this->setDeliveryCost(0);

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
     * @return BaseOrder
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
     * Set creationDate
     *
     * @param \DateTime $creationDate
     * @return BaseOrder
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * Get creationDate
     *
     * @return \DateTime
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Set updateDate
     *
     * @param \DateTime $updateDate
     * @return BaseOrder
     */
    public function setUpdateDate($updateDate)
    {
        $this->updateDate = $updateDate;

        return $this;
    }

    /**
     * Get updateDate
     *
     * @return \DateTime
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
    }

    /**
     * Set validationDate
     *
     * @param \DateTime $validationDate
     * @return BaseOrder
     */
    public function setValidationDate($validationDate)
    {
        if($this->validationDate != null) return $this;

        $this->validationDate = $validationDate;

        $this->shipmentsCalculation();

        return $this;
    }

    /**
     * Get validationDate
     *
     * @return \DateTime
     */
    public function getValidationDate()
    {
        return $this->validationDate;
    }

    /**
     * Set deliver
     *
     * @param string $deliver
     * @return BaseOrder
     */
    public function setDeliver($deliver)
    {
        $this->deliver = $deliver;

        return $this;
    }

    /**
     * Get deliver
     *
     * @return string
     */
    public function getDeliver()
    {
        return $this->deliver;
    }

    /**
     * Set deliveryMethod
     *
     * @param string $deliveryMethod
     * @return BaseOrder
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
     * @return BaseOrder
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
     * Set deliveryState
     *
     * @param integer $deliveryState
     * @return BaseOrder
     */
    public function setDeliveryState($deliveryState)
    {
        $this->deliveryState = $deliveryState;

        return $this;
    }

    /**
     * Get deliveryState
     *
     * @return integer
     */
    public function getDeliveryState()
    {
        return $this->deliveryState;
    }

    /**
     * Set payementMethod
     *
     * @param string $payementMethod
     * @return BaseOrder
     */
    public function setPayementMethod($payementMethod)
    {
        $this->payementMethod = $payementMethod;

        return $this;
    }

    /**
     * Get payementMethod
     *
     * @return string
     */
    public function getPayementMethod()
    {
        return $this->payementMethod;
    }

    /**
     * Set payementInfos
     *
     * @param string $payementInfos
     * @return BaseOrder
     */
    public function setPayementInfos($payementInfos)
    {
        $this->payementInfos = $payementInfos;

        return $this;
    }

    /**
     * Get payementInfos
     *
     * @return string
     */
    public function getPayementInfos()
    {
        return $this->payementInfos;
    }

    /**
     * Set payementState
     *
     * @param string $payementState
     * @return BaseOrder
     */
    public function setPayementState($payementState)
    {
        $this->payementState = $payementState;

        return $this;
    }

    /**
     * Get payementState
     *
     * @return string
     */
    public function getPayementState()
    {
        return $this->payementState;
    }

    /**
     * Set reference
     *
     * @param string $reference
     * @return BaseOrder
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
     * Set invoiceReference
     *
     * @param string $invoiceReference
     * @return BaseOrder
     */
    public function setInvoiceReference($invoiceReference)
    {
        $this->invoiceReference = $invoiceReference;

        return $this;
    }

    /**
     * Get invoiceReference
     *
     * @return string
     */
    public function getInvoiceReference()
    {
        return $this->invoiceReference;
    }

    /**
     * Set shippingMessage
     *
     * @param string $shippingMessage
     * @return BaseOrder
     */
    public function setShippingMessage($shippingMessage)
    {
        $this->shippingMessage = $shippingMessage;

        $this->shipmentsCalculation();

        return $this;
    }

    /**
     * Get shippingMessage
     *
     * @return string
     */
    public function getShippingMessage()
    {
        return $this->shippingMessage;
    }

    /**
     * Set locale
     *
     * @param string $locale
     * @return BaseOrder
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return BaseOrder
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set state
     *
     * @param integer $state
     * @return BaseOrder
     */
    public function setState($state)
    {
        //On retient si le state a changé
        if($this->state != $state)
            $this->oldState = $this->state;

        $this->state = $state;

        //Si la commande est marquée comme finalisée on marque comme étant finalisés tous les envois
        if($state == 9)
        {
            foreach($this->getShipments() as $shipment)
                $shipment->setState(9);
        }
        else $this->mustRecaculShipments = true;

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
     * Set idDeliver
     *
     * @param integer $idDeliver
     * @return BaseOrder
     */
    public function setIdDeliver($idDeliver)
    {
        $this->idDeliver = $idDeliver;

        return $this;
    }

    /**
     * Get idDeliver
     *
     * @return integer
     */
    public function getIdDeliver()
    {
        return $this->idDeliver;
    }

    /**
     * Set billingUser
     *
     * @param \Dywee\UserBundle\Entity\User $billingUser
     * @return BaseOrder
     */
    public function setBillingUser(\Dywee\UserBundle\Entity\User $billingUser = null)
    {
        $this->billingUser = $billingUser;

        return $this;
    }

    /**
     * Get billingUser
     *
     * @return \Dywee\UserBundle\Entity\User
     */
    public function getBillingUser()
    {
        return $this->billingUser;
    }

    /**
     * Set shippingUser
     *
     * @param \Dywee\UserBundle\Entity\User $shippingUser
     * @return BaseOrder
     */
    public function setShippingUser(\Dywee\UserBundle\Entity\User $shippingUser = null)
    {
        $this->shippingUser = $shippingUser;

        return $this;
    }

    /**
     * Get shippingUser
     *
     * @return \Dywee\UserBundle\Entity\User
     */
    public function getShippingUser()
    {
        return $this->shippingUser;
    }

    /**
     * Set billingAddress
     *
     * @param \Dywee\AddressBundle\Entity\Address $billingAddress
     * @return BaseOrder
     */
    public function setBillingAddress(\Dywee\AddressBundle\Entity\Address $billingAddress = null)
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    /**
     * Get billingAddress
     *
     * @return \Dywee\AddressBundle\Entity\Address
     */
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    /**
     * Set shippingAddress
     *
     * @param \Dywee\AddressBundle\Entity\Address $shippingAddress
     * @return BaseOrder
     */
    public function setShippingAddress(\Dywee\AddressBundle\Entity\Address $shippingAddress = null)
    {
        $this->shippingAddress = $shippingAddress;

        return $this;
    }

    /**
     * Get shippingAddress
     *
     * @return \Dywee\AddressBundle\Entity\Address
     */
    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }

    /**
     * Set discountValue
     *
     * @param float $discountValue
     * @return BaseOrder
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
     * Constructor
     */
    public function __construct()
    {
        $this->setCreationDate(new \DateTime());
        $this->orderElements = new \Doctrine\Common\Collections\ArrayCollection();
        $this->shipments = new \Doctrine\Common\Collections\ArrayCollection();
        $this->reference = time().'-'.strtoupper(substr(md5(rand().rand()), 0, 4));
    }

    /**
     * Add orderElements
     *
     * @param \Dywee\OrderBundle\Entity\OrderElement $orderElements
     * @return BaseOrder
     */
    public function addOrderElement(\Dywee\OrderBundle\Entity\OrderElement $orderElements)
    {
        $this->orderElements[] = $orderElements;
        $orderElements->setOrder($this);

        return $this;
    }

    /**
     * Remove orderElements
     *
     * @param \Dywee\OrderBundle\Entity\OrderElement $orderElements
     */
    public function removeOrderElement(\Dywee\OrderBundle\Entity\OrderElement $orderElements)
    {
        $this->orderElements->removeElement($orderElements);
        $orderElements->setOrder(null);
        $this->mustRecaculShipments = true;
    }

    /**
     * Get orderElements
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrderElements()
    {
        return $this->orderElements;
    }

    /**
     * Add shipments
     *
     * @param \Dywee\ShipmentBundle\Entity\Shipment $shipment
     * @return BaseOrder
     */
    public function addShipment(\Dywee\ShipmentBundle\Entity\Shipment $shipment)
    {
        $this->shipments[] = $shipment;
        $shipment->setOrder($this);

        return $this;
    }

    /**
     * Remove shipments
     *
     * @param \Dywee\ShipmentBundle\Entity\Shipment $shipments
     */
    public function removeShipment(\Dywee\ShipmentBundle\Entity\Shipment $shipments)
    {
        $this->shipments->removeElement($shipments);
        $shipments->setOrder(null);
    }

    /**
     * Get shipments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getShipments()
    {
        return $this->shipments;
    }

    /**
     * Set old_id
     *
     * @param integer $oldId
     * @return BaseOrder
     */
    public function setOldId($oldId)
    {
        $this->old_id = $oldId;

        return $this;
    }

    /**
     * Get old_id
     *
     * @return integer
     */
    public function getOldId()
    {
        return $this->old_id;
    }

    public function countProducts($type = null)
    {
        $nbre = 0;

        foreach($this->getOrderElements() as $orderElement)
        {
            if($type == null || $orderElement->getProduct()->getProductType() == $type)
                $nbre += $orderElement->getQuantity();
        }

        return $nbre;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function forcePriceCalculation()
    {
        $isTTC = true;
        // CALCUL DU PRIX
        $price = 0;
        if($this->getShippingAddress() != null && $this->getShippingAddress()->getCountry() != null)
            $this->setVatRate($this->getShippingAddress()->getCountry()->getVatRate());
        $this->setPriceVatIncl(0);
        foreach($this->getOrderElements() as $orderElement)
            $price += $orderElement->getTotalPrice();


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
    * @ORM\PrePersist
    * @ORM\PreUpdate
    */
    public function shipmentsCalculation($force = false)
    {
        if($this->mustRecaculShipments || $force)
        {
            if(count($this->getShipments()) > 0)
                foreach($this->getShipments() as $shipment)
                    $this->removeShipment($shipment);

            $productShipment = new Shipment();
            $departureDate = $this->getValidationDate() == null ? new \DateTime() : $this->getValidationDate();
            $departureDate->modify('+1day');
            $productShipment->setDepartureDate($departureDate);
            $productShipment->setState(0);

            foreach($this->getOrderElements() as $orderElement)
            {
                if($orderElement->getProduct()->getProductType() > 1)
                {
                    for($j = 0; $j < $orderElement->getQuantity(); $j++)
                    {
                        $shipment = new Shipment();
                        $departureDate = $this->getValidationDate() == null ? new \DateTime() : $this->getValidationDate();
                        $departureDate->modify('+1day');
                        $shipment->setDepartureDate($departureDate);
                        $shipment->setState(0);

                        $shipmentElement = new ShipmentElement();
                        $shipmentElement->setQuantity($orderElement->getQuantity());
                        $shipmentElement->setProduct($orderElement->getProduct());
                        $shipmentElement->setWeight(($orderElement->getProduct()->getWeight()*$orderElement->getQuantity()));
                        $shipment->addShipmentElement($shipmentElement);

                        $this->addShipment($shipment);

                        if($orderElement->getProduct()->getProductType() == 3)
                        {
                            $shipment->setSendingIndex(1);

                            $departureDate = $this->getValidationDate() == null ? new \DateTime() : $this->getValidationDate();
                            $departureDay = (int) $departureDate->format('d');
                            $departureMonth = (int) $departureDate->format('m');
                            $departureYear = (int) $departureDate->format('Y');

                            if($departureDay > 20)
                                $departureMonth++;

                            $departureDay = 10;

                            for($i=1; $i< $orderElement->getProduct()->getRecurrence(); $i++)
                            {
                                $departureMonth++;
                                if($departureMonth > 12){
                                    $departureMonth -= 12;
                                    $departureYear++;
                                }

                                $shipment = new Shipment();

                                $departure = new \DateTime($departureYear.'/'.$departureMonth.'/'.$departureDay);
                                $shipment->setDepartureDate($departure);
                                $shipment->setState(0);
                                $shipment->setSendingIndex($i+1);

                                $shipmentElement = new ShipmentElement();
                                $shipmentElement->setQuantity($orderElement->getQuantity());
                                $shipmentElement->setProduct($orderElement->getProduct());
                                $shipmentElement->setWeight(($orderElement->getProduct()->getWeight()*$orderElement->getQuantity()));
                                $shipment->addShipmentElement($shipmentElement);

                                $this->addShipment($shipment);
                            }
                        }
                    }
                }
                elseif($orderElement->getProduct()->getProductType() == 1)
                {
                    $shipmentElement = new ShipmentElement();
                    $shipmentElement->setQuantity($orderElement->getQuantity());
                    $shipmentElement->setProduct($orderElement->getProduct());
                    $shipmentElement->setWeight(($orderElement->getProduct()->getWeight()*$orderElement->getQuantity()));
                    $productShipment->addShipmentElement($shipmentElement);
                }
            }

            if(count($productShipment->getShipmentElements())>0)
                $this->addShipment($productShipment);
        }
        //$shipment->calculWeight();
        $this->forcePriceCalculation();
        $this->mustRecaculShipments = false;

        return $this;
    }

    public function addProduct(\Dywee\ProductBundle\Entity\Product $product, $quantity, $locationCoeff = 1)
    {
        $exist = false;
        //Check si le produit a déjà été commandé une fois
        foreach($this->getOrderElements() as $key => $orderElement) {
            if ($orderElement->getProduct()->getId() == $product->getId()) {
                //Si oui on augmente la quantité
                $orderElement->setQuantity($orderElement->getQuantity() + $quantity);
                if($orderElement->getQuantity() <= 0)$this->removeOrderElement($orderElement);
                $exist = $key;
            }
        }

        //Sinon on l'ajoute
        if(!is_numeric($exist))
        {
            $orderElement = new OrderElement();

            $orderElement->setProduct($product);
            $orderElement->setQuantity($quantity);
            $orderElement->setLocationCoeff($locationCoeff);

            $this->addOrderElement($orderElement);
        }

        $this->forcePriceCalculation();

        $this->mustRecaculShipments = true;

        return $this;
    }

    /**
     * Set weight
     *
     * @param float $weight
     * @return BaseOrder
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
    public function getWeight($byType = false)
    {
        if(is_numeric($byType))
        {
            $weight = 0;
            foreach($this->getOrderElements() as $orderElement)
                if($orderElement->getProduct()->getProductType() == $byType)
                    $weight += $orderElement->getProduct()->getWeight() * $orderElement->getQuantity();
            return $weight;
        }
        else return $this->weight;
    }

    /**
     * @return $this
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function weightCalculation()
    {
        $weight = 0;
        foreach($this->getOrderElements() as $key => $orderElement) {
            $weight += $orderElement->getProduct()->getWeight()*$orderElement->getQuantity();
        }
        $this->setWeight($weight);
        return $this;
    }

    /**
     * Set shippingMethod
     *
     * @param \Dywee\ShipmentBundle\Entity\ShipmentMethod $shippingMethod
     * @return BaseOrder
     */
    public function setShippingMethod(\Dywee\ShipmentBundle\Entity\ShipmentMethod $shippingMethod = null)
    {
        $this->shippingMethod = $shippingMethod;

        return $this;
    }

    /**
     * Get shippingMethod
     *
     * @return \Dywee\ShipmentBundle\Entity\ShipmentMethod
     */
    public function getShippingMethod()
    {
        return $this->shippingMethod;
    }

    public function removeProduct($product)
    {
        $id = is_numeric($product) ? $product : $product->getId();

        foreach($this->getOrderElements() as $orderElement)
        {
            if($orderElement->getProduct()->getId() == $id)
                $this->removeOrderElement($orderElement);

        }

        $this->mustRecaculShipments = true;

        $this->forcePriceCalculation();

        return $this;
    }

    /**
     * Add deliver
     *
     * @param \Dywee\ShipmentBundle\Entity\Deliver $deliver
     * @return BaseOrder
     */
    public function addDeliver(\Dywee\ShipmentBundle\Entity\Deliver $deliver)
    {
        $this->deliver[] = $deliver;

        return $this;
    }

    /**
     * Remove deliver
     *
     * @param \Dywee\ShipmentBundle\Entity\Deliver $deliver
     */
    public function removeDeliver(\Dywee\ShipmentBundle\Entity\Deliver $deliver)
    {
        $this->deliver->removeElement($deliver);
    }

    public function setFromOffer($offer)
    {
        $this->setShippingAddress($offer->getAddress());
        $this->setBillingAddress($offer->getAddress());

        $this->setPriceVatExcl($offer->getPriceVatExcl());
        $this->setDeliveryCost($offer->getDeliveryCost());
        $this->setDeliver($offer->getDeliver());
        $this->setVatPrice($offer->getVatPrice());
        $this->setVatRate($offer->getVatRate());
        $this->setPriceVatIncl($offer->getPriceVatIncl());
        $this->setDiscountRate($offer->getDiscountRate());
        $this->setDiscountValue($offer->getDiscountValue());
        $this->setTotalPrice($offer->getTotalPrice());
        $this->setState(1);

        foreach($offer->getOfferElements() as $offerElement)
        {
            $orderElement = new OrderElement();
            $orderElement->setProduct($offerElement->getProduct());
            $orderElement->setQuantity($offerElement->getQuantity());
            $orderElement->setDuration($offerElement->getDuration());
            $orderElement->setLocationCoeff($offerElement->getLocationCoeff());
            $orderElement->setUnitPrice($offerElement->getUnitPrice());
            $orderElement->setTotalPrice($offerElement->getTotalPrice());
            $orderElement->setDiscountRate($offerElement->getDiscountRate(), false);
            $orderElement->setDiscountValue($offerElement->getDiscountValue(), false);

            $this->addOrderElement($orderElement);
        }
    }

    public function containsElementReduction()
    {
        foreach($this->orderElements as $orderElement)
            if($orderElement->getDiscountValue() > 0)return true;
        return false;
    }

    /**
     * Set deposit
     *
     * @param float $deposit
     * @return BaseOrder
     */
    public function setDeposit($deposit)
    {
        $this->deposit = $deposit;

        return $this;
    }

    /**
     * Get deposit
     *
     * @return float
     */
    public function getDeposit()
    {
        return $this->deposit;
    }

    /**
     * Set endingDate
     *
     * @param \DateTime $endingDate
     * @return BaseOrder
     */
    public function setEndingDate($endingDate)
    {
        $this->endingDate = $endingDate;

        return $this;
    }

    /**
     * Get endingDate
     *
     * @return \DateTime
     */
    public function getEndingDate()
    {
        return $this->endingDate;
    }

    /**
     * Set duration
     *
     * @param integer $duration
     * @return BaseOrder
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

    public function containsOnlyOneType($type = null)
    {
        $wType = false;
        $sameType = true;
        foreach($this->getOrderElements() as $orderElement)
        {
            $xType = $orderElement->getProduct()->getProductType();
            if(!$wType)$wType = $xType;
            else if($xType != $wType) $sameType = false;
        }
        if($sameType)
        {
            if(is_numeric($type))
                return $type == $wType;

            else return true;
        }
        else return false;
    }

    /**
     * Set isPriceTTC
     *
     * @param boolean $isPriceTTC
     *
     * @return BaseOrder
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
     * @return BaseOrder
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

    public function countSendedShipments()
    {
        $counter = 0;
        foreach($this->getShipments() as $shipment)
        {
            if($shipment->getState() == 9)
                $counter++;
        }
        return $counter;
    }

    public function checkIfIsDone()
    {
        if(count($this->getShipments()) == $this->countSendedShipments()) {
            $this->setState(9);
            return 0;
        }
        else return count($this->getShipments())-$this->countSendedShipments();
    }

    /**
     * Set mailSended
     *
     * @param boolean $mailSended
     *
     * @return BaseOrder
     */
    public function setMailSended($mailSended)
    {
        $this->mailSended = $mailSended;
        return $this;
    }

    /**
     * Get mailSended
     *
     * @return boolean
     */
    public function getMailSended()
    {
        return $this->mailSended;
    }

    /**
     * Set collectionAt
     *
     * @param \DateTime $date
     * @return BaseOrder
     */
    public function setCollectionAt($date)
    {
        $this->collectionAt = $date;
        return $this;
    }

    /**
     * Get collectionAt
     *
     * @return \DateTime
     */
    public function getCollectionAt()
    {
        return $this->collectionAt;
    }

    /**
     * Set returnFor
     *
     * @param \DateTime $date
     * @return BaseOrder
     */
    public function setReturnFor($date)
    {
        $this->returnFor = $date;
        return $this;
    }

    /**
     * Get returnFor
     *
     * @return \DateTime
     */
    public function getReturnFor()
    {
        return $this->returnFor;
    }

    /**
     * Set returnedAt
     *
     * @param \DateTime $date
     * @return BaseOrder
     */
    public function setReturnedAt($date)
    {
        $this->returnedAt = $date;
        return $this;
    }

    /**
     * Get returnedAt
     *
     * @return \DateTime
     */
    public function getReturnedAt()
    {
        return $this->returnedAt;
    }

    public function decreaseStock()
    {
        foreach($this->getOrderElements() as $orderElement)
            $orderElement->getProduct()->decreaseStock($orderElement->getQuantity());

        return $this;
    }

    public function refundStock()
    {
        foreach($this->getOrderElements() as $orderElement)
            $orderElement->getProduct()->refundStock($orderElement->getQuantity());

        return $this;
    }

    public function getOldState()
    {
        return $this->oldState;
    }

    /**
     * Set website
     *
     * @param \Dywee\WebsiteBundle\Entity\Website $website
     * @return BaseOrder
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

    public function isVirtual()
    {
        foreach ($this->getOrderElements() as $element)
            if ($element->getProduct()->getSellType() == 2)
                return true;
        return false;
    }
}
