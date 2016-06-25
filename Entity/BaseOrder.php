<?php

namespace Dywee\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Dywee\AddressBundle\Entity\Address;
use Dywee\ProductBundle\Entity\BaseProduct;
use Dywee\ProductBundle\Entity\ProductDownloadable;
use Dywee\ShipmentBundle\Entity\Shipment;
use Dywee\ShipmentBundle\Entity\ShipmentElement;
use Dywee\UserBundle\Entity\User;

/**
 * BaseOrder
 *
 * @ORM\Table(name="orders")
 * @ORM\Entity(repositoryClass="Dywee\OrderBundle\Repository\BaseOrderRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class BaseOrder
{
    const STATE_IN_SESSION = 'order.state_session';
    const STATE_CANCELLED = 'order.state_cancelled';
    const STATE_WAITING = 'order.state_waiting';
    const STATE_IN_PROGRESS = 'order.state_in_progress';
    const STATE_FINALIZED = 'order.state_finalized';
    const STATE_RETURNED = 'order.state_returned';

    const STATE_READY_FOR_SHIPPING = 'order.state_ready_shipping';
    const STATE_IN_SHIPPING = 'order.state_in_shipping';

    const STATE_CUSTOMER_ERROR = 'order.state_customer_error';
    const STATE_SELLER_ERROR = 'order.state_seller_customer';
    const STATE_ERROR = 'order.state_error';

    const PAYMENT_STATE_WAITING = 'order_payment.state_waiting';
    const PAYMENT_WAITING_VALIDATION = 'order_payment.state_waiting';
    const PAYMENT_VALIDATED = 'order_payment.state_validated';
    const PAYMENT_REFUND = 'order_payment.state_refund';


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
    private $isGift = false;

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
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $validatedAt;

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
     * @ORM\Column(type="string", length=255)
     */
    private $paymentState = self::PAYMENT_WAITING_VALIDATION;

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
    private $active = false;

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
    private $state = self::STATE_IN_SESSION;

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
     * @ORM\Column(name="mailStep", type="smallint")
     */
    private $mailStep = false;

    /**
     * @ORM\Column(name="isPriceTTC", type="boolean")
     */
    private $isPriceTTC = true;


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
     * Set createdAt
     *
     * @param \DateTime $creationDate
     * @return BaseOrder
     */
    public function setCreatedAt(\DateTime $creationDate)
    {
        $this->createdAt = $creationDate;

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
     * Set updatedAt
     *
     * @param \DateTime $updateDate
     * @return BaseOrder
     */
    public function setUpdatedAt(\DateTime $updateDate)
    {
        $this->updatedAt = $updateDate;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set validatedAt
     *
     * @param \DateTime $validationDate
     * @return BaseOrder
     */
    public function setValidatedAt(\DateTime $validationDate)
    {
        if($this->validatedAt != null) return $this;

        $this->validatedAt = $validationDate;

        $this->shipmentsCalculation();

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
     * Set paymentState
     *
     * @param string $paymentState
     * @return BaseOrder
     */
    public function setPaymentState($paymentState)
    {
        $this->paymentState = $paymentState;

        return $this;
    }

    /**
     * Get paymentState
     *
     * @return string
     */
    public function getPaymentState()
    {
        return $this->paymentState;
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
     * Set billingUser
     *
     * @param \Dywee\UserBundle\Entity\User $billingUser
     * @return BaseOrder
     */
    public function setBillingUser(User $billingUser = null)
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
     * @param User $shippingUser
     * @return BaseOrder
     */
    public function setShippingUser(User $shippingUser = null)
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
    public function setBillingAddress(Address $billingAddress = null)
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
    public function setShippingAddress(Address $shippingAddress = null)
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
        $this->createdAt = new \DateTime();
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

    public function addProduct(BaseProduct $product, $quantity, $locationCoeff = 1)
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
     * Set endedAt
     *
     * @param \DateTime $endingDate
     * @return BaseOrder
     */
    public function setEndedAt(\DateTime $endingDate)
    {
        $this->endedAt = $endingDate;

        return $this;
    }

    /**
     * Get endedAt
     *
     * @return \DateTime
     */
    public function getEndedAt()
    {
        return $this->endedAt;
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
     * Set mailStep
     *
     * @param integer $mailStep
     *
     * @return BaseOrder
     */
    public function setMailStep($mailStep)
    {
        $this->mailStep = $mailStep;
        return $this;
    }

    /**
     * Get mailStep
     *
     * @return boolean
     */
    public function getMailStep()
    {
        return $this->mailStep;
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

    private $isVirtual = null;
    private $isOnlyVirtual = null;

    public function isVirtual($forceRecalcul = false)
    {
        if($this->isVirtual && !$forceRecalcul)
            return $this->isVirtual;

        $this->calculVirtualisation();

        return $this->isVirtual;
    }

    public function isOnlyVirtual($forceRecalcul = false)
    {
        if($this->isOnlyVirtual && !$forceRecalcul)
            return $this->isVirtual;

        $this->calculVirtualisation();

        return $this->isOnlyVirtual;
    }

    private function calculVirtualisation()
    {
        $this->isVirtual = false;

        if($this->getOrderElements() < 1)
            $this->isOnlyVirtual = false;
        else $this->isOnlyVirtual = true;



        foreach ($this->getOrderElements() as $element)
            if ($element->getProduct() instanceof ProductDownloadable)
                $this->isVirtual = true;
            else
                $this->isOnlyVirtual = false;
    }
}
