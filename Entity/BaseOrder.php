<?php

namespace Dywee\OrderBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Dywee\CoreBundle\Model\AddressInterface;
use Dywee\CoreBundle\Model\ProductInterface;
use Dywee\CoreBundle\Traits\TimeDelimitableEntity;
use Dywee\ProductBundle\Entity\BaseProduct;
use Dywee\ProductBundle\Entity\RentableProduct;
use Dywee\ProductBundle\Entity\RentableProductItem;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Dywee\CoreBundle\Model\CustomerInterface;
use Payum\Core\Model\PaymentInterface;

/**
 * BaseOrder
 *
 * @ORM\Table(name="orders")
 * @ORM\Entity(repositoryClass="Dywee\OrderBundle\Repository\BaseOrderRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class BaseOrder implements BaseOrderInterface
{
    use TimestampableEntity;
    use TimeDelimitableEntity;


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
     * @ORM\Column(name="priceVatExcl", type="decimal", precision=5, scale=2)
     */
    private $priceVatExcl = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="deliveryCost", type="decimal", precision=5, scale=2)
     */
    private $deliveryCost = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="vatRate", type="decimal", precision=5, scale=2)
     */
    private $vatRate = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="vatPrice", type="decimal", precision=5, scale=2)
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
     * @ORM\Column(name="discountRate", type="decimal", precision=5, scale=2, nullable=true)
     */
    private $discountRate;

    /**
     * @var float
     *
     * @ORM\Column(name="discountValue", type="decimal", precision=5, scale=2, nullable=true)
     */
    private $discountValue;

    /**
     * @var float
     *
     * @ORM\Column(name="priceVatIncl", type="decimal", precision=5, scale=2)
     */
    private $priceVatIncl = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="totalPrice", type="decimal", precision=5, scale=2)
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
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $validatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="reference", type="string", length=255)
     */
    private $reference;

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
     * @var string
     *
     * @ORM\Column(name="state", type="text")
     */
    private $state = BaseOrderInterface::STATE_IN_SESSION;

    /**
     * @var CustomerInterface
     * @ORM\ManyToOne(targetEntity="Dywee\CoreBundle\Model\CustomerInterface", cascade={"persist"})
     */
    private $customer;

    /**
     * @var AddressInterface
     * @ORM\ManyToOne(targetEntity="Dywee\AddressBundle\Entity\Address", cascade={"persist"})
     */
    private $billingAddress;

    /**
     * @var AddressInterface
     * @ORM\ManyToOne(targetEntity="Dywee\AddressBundle\Entity\Address", cascade={"persist"})
     */
    private $shippingAddress;

    /**
     * @ORM\OneToMany(targetEntity="OrderElement", mappedBy="order", cascade={"persist", "remove"})
     */
    private $orderElements;

    /**
     * @ORM\OneToMany(targetEntity="OrderDiscountElement", mappedBy="order")
     */
    private $discountElements;

    /**
     * @ORM\OneToMany(targetEntity="Dywee\OrderBundle\Entity\Shipment", mappedBy="order", cascade={"persist", "remove"})
     */
    private $shipments;

    /**
     * @ORM\ManyToOne(targetEntity="Dywee\OrderBundle\Entity\ShippingMethod", cascade={"persist"})
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

    /**
     * @var string
     * @ORM\Column(type="string", length=15)
     */
    private $type = self::TYPE_ONLY_BUY;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    private $mustRecalculShipments = false;


    private $previousState;

    /**
     * @var ArrayCollection|Payment[]
     * @ORM\OneToMany(targetEntity="Dywee\OrderBundle\Entity\Payment", mappedBy="order")
     */
    private $payments;

    /**
     * @var string
     * @ORM\Column(type="string", length=30)
     */
    private $paymentStatus = BaseOrderInterface::PAYMENT_STATUS_NONE;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->orderElements = new ArrayCollection();
        $this->shipments = new ArrayCollection();
        $this->reference = time() . '-' . strtoupper(substr(md5(mt_rand() . mt_rand()), 0, 4));
        $this->discountElements = new ArrayCollection();
        $this->beginAt = new \DateTime();
        $this->payments = new ArrayCollection();
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
     * Set isGift
     *
     * @param boolean $isGift
     *
     * @return BaseOrderInterface
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
    public function isGift()
    {
        return $this->isGift;
    }

    /**
     * Set priceVatExcl
     *
     * @param float $priceVatExcl
     *
     * @return BaseOrderInterface
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
     * @deprecated
     *
     * @param float $deliveryCost
     *
     * @return BaseOrderInterface
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
     * @deprecated use shippingCost instead
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
     *
     * @return BaseOrderInterface
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
     *
     * @return BaseOrderInterface
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
     *
     * @return BaseOrderInterface
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
     *
     * @return BaseOrderInterface
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
     *
     * @return BaseOrderInterface
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
     *
     * @return BaseOrderInterface
     */
    public function setTotalPrice($totalPrice)
    {
        $this->totalPrice = $totalPrice;
        if ($this->totalPrice === 0) {
            $this->setShippingCost(0);
        }

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
     *
     * @return BaseOrderInterface
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
     * Set validatedAt
     *
     * @param \DateTime $validationDate
     *
     * @return BaseOrderInterface
     */
    public function setValidatedAt(\DateTime $validationDate)
    {
        $this->validatedAt = $validationDate;

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
     *
     * @return BaseOrderInterface
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
     *
     * @return BaseOrderInterface
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
     *
     * @return BaseOrderInterface
     */
    public function setShippingMessage($shippingMessage)
    {
        $this->shippingMessage = $shippingMessage;

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
     *
     * @return BaseOrderInterface
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
     *
     * @return BaseOrderInterface
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
     *
     * @return BaseOrderInterface
     */
    public function setState($state)
    {
        // We keep the previous state for handling the change of state
        if ($this->state !== $state) {
            $this->setPreviousState($this->getState());
        }

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
     * Set customer
     *
     * @param CustomerInterface $customer
     *
     * @return BaseOrderInterface
     */
    public function setCustomer(CustomerInterface $customer = null)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get customer
     *
     * @return CustomerInterface
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @inheritdoc
     */
    public function setBillingAddress(AddressInterface $billingAddress = null)
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    /**
     * @inheritdoc
     */
    public function setShippingAddress(AddressInterface $shippingAddress = null)
    {
        $this->shippingAddress = $shippingAddress;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }

    /**
     * @inheritdoc
     */
    public function setDiscountValue($discountValue)
    {
        $this->discountValue = $discountValue;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDiscountValue()
    {
        return $this->discountValue;
    }

    /**
     * @inheritdoc
     */
    public function addOrderElement(OrderElement $orderElements)
    {
        $this->orderElements[] = $orderElements;
        $orderElements->setOrder($this);
        $this->mustRecalculShipments = true;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function removeOrderElement(OrderElement $orderElements)
    {
        $this->orderElements->removeElement($orderElements);
        $orderElements->setOrder(null);
        $this->mustRecalculShipments = true;

        return $this;
    }

    /**
     * Get orderElements
     *
     * @return \Doctrine\Common\Collections\Collection|OrderElement[]
     */
    public function getOrderElements()
    {
        return $this->orderElements;
    }


    /**
     * @param null $type
     *
     * @return int
     */
    public function countProducts($type = null)
    {
        $quantity = 0;

        foreach ($this->getOrderElements() as $orderElement) {
            if (!$type || $orderElement->getProduct() instanceof $type) {
                $quantity += $orderElement->getQuantity();
            }
        }

        return $quantity;
    }

    /**
     * @inheritdoc
     */
    public function forcePriceCalculation()
    {
        $isTTC = true;

        // Price calculation
        $price = 0;
        if ($this->getShippingAddress() && $this->getShippingAddress()->getCity() && $this->getShippingAddress()->getCity()->getCountry()) {
            $this->setVatRate($this->getShippingAddress()->getCity()->getCountry()->getVatRate());
        }
        $this->setPriceVatIncl(0);
        foreach ($this->getOrderElements() as $orderElement) {
            $price += $orderElement->getTotalPrice();
        }

        if ($isTTC) {
            $this->setPriceVatIncl($price);
            $this->setPriceVatExcl($price / (1 + $this->getVatRate() / 100));
            $this->setVatPrice($this->getPriceVatIncl() - $this->getPriceVatExcl());
        }

        $this->calculateShippingCost();

        $this->setTotalPrice($this->getPriceVatIncl() + $this->getShippingCost() - $this->getDiscountValue());

        return $this;
    }

    /**
     * @return BaseOrder
     * @deprecated
     */
    public function calculShippingCost()
    {
        return $this->calculateShippingCost();
    }

    /**
     * @return $this
     */
    public function calculateShippingCost()
    {
        //TODO le shipping cost doit être calculé via le prix d'envoi de chaque shippingMethod de chaque orderElement
        if ($this->getShippingMethod()) {
            $this->setShippingCost($this->getShippingMethod()->getPrice() * count($this->getShipments()));
        } else {
            $this->setShippingCost(null);
        }

        return $this;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function checkBeforeDB()
    {
        $this->checkType();
        //$this->weightCalculation();
        $this->forcePriceCalculation();
    }

    public function checkType()
    {
        $buy = false;
        $rent = false;

        foreach ($this->getOrderElements() as $element) {
            if ($element instanceof RentableProduct || $element instanceof RentableProductItem) {
                $rent = true;
            } else {
                $buy = true;
            }

            if ($rent && $buy) {
                break;
            }
        }

        if ($buy) {
            if ($rent) {
                $this->setType(BaseOrder::TYPE_BUY_AND_RENT);
            } else {
                $this->setType(BaseOrder::TYPE_ONLY_BUY);
            }
        } elseif ($rent) {
            $this->setType(BaseOrder::TYPE_BUY_AND_RENT);
        }

        return $this;

    }

    /**
     * @param ProductInterface $product
     * @param             $quantity
     * @param int         $locationCoeff
     *
     * @return $this
     */
    public function addProduct(ProductInterface $product, $quantity, $locationCoeff = 1)
    {

    }

    /**
     * @inheritdoc
     */
    public function getQuantityForProduct(ProductInterface $product)
    {
        foreach ($this->getOrderElements() as $key => $orderElement) {
            if ($orderElement->getProduct()->getId() === $product->getId()) {
                return $orderElement->getQuantity();
            }
        }

        return 0;
    }


    /**
     * @inheritdoc
     */
    public function getWeight($type = null)
    {
        $weight = 0;
        foreach ($this->getOrderElements() as $orderElement) {
            if (!$type || $orderElement->getProduct() instanceof $type) {
                $weight += $orderElement->getProduct()->getWeight() * $orderElement->getQuantity();
            }
        }

        return $weight;
    }

    /**
     * @return $this
     */
    public function weightCalculation()
    {
        $weight = 0;
        foreach ($this->getOrderElements() as $key => $orderElement) {
            $weight += $orderElement->getProduct()->getWeight() * $orderElement->getQuantity();
        }
        $this->weight = $weight;

        return $this;
    }


    public function removeProduct(ProductInterface $product)
    {
        $id = is_numeric($product) ? $product : $product->getId();

        foreach ($this->getOrderElements() as $orderElement) {
            if ($orderElement->getProduct()->getId() === $id) {
                $this->removeOrderElement($orderElement);
                $this->mustRecalculShipments = true;
                $this->forcePriceCalculation();
            }
        }

        return $this;
    }


    public function containsElementReduction()
    {
        foreach ($this->orderElements as $orderElement) {
            if ($orderElement->getDiscountValue() > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function setDeposit($deposit)
    {
        $this->deposit = $deposit;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDeposit()
    {
        return $this->deposit;
    }

    /**
     * @inheritdoc
     */
    public function setEndedAt(\DateTime $endingDate)
    {
        $this->endedAt = $endingDate;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getEndedAt()
    {
        return $this->endedAt;
    }

    /**
     * @param null $type
     *
     * @return bool
     */
    public function containsOnlyOneType($type = null)
    {
        $wType = false;
        $sameType = true;
        foreach ($this->getOrderElements() as $orderElement) {
            $xType = $orderElement->getProduct()->getValidatedAt();
            if (!$wType) {
                $wType = $xType;
            } elseif ($xType !== $wType) {
                $sameType = false;
            }
        }
        if ($sameType) {
            if (is_numeric($type)) {
                return $type === $wType;
            }

            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function setIsPriceTTC($isPriceTTC)
    {
        $this->isPriceTTC = $isPriceTTC;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getIsPriceTTC()
    {
        return $this->isPriceTTC;
    }


    /**
     * @inheritdoc
     */
    public function setMailStep($mailStep)
    {
        $this->mailStep = $mailStep;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMailStep()
    {
        return $this->mailStep;
    }

    /**
     * @inheritdoc
     */
    public function decreaseStock()
    {
        foreach ($this->getOrderElements() as $orderElement) {
            $orderElement->getProduct()->decreaseStock($orderElement->getQuantity());
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function refundStock()
    {
        foreach ($this->getOrderElements() as $orderElement) {
            $orderElement->getProduct()->refundStock($orderElement->getQuantity());
        }

        return $this;
    }

    public function setPreviousState($state)
    {
        $this->previousState = $state;

        return $this;
    }

    public function getPreviousState()
    {
        return $this->previousState;
    }

    /**
     * alias
     */
    public function getPrice()
    {
        return $this->getTotalPrice();
    }

    /**
     * alias
     *
     * @param $price
     *
     * @return BaseOrderInterface
     */
    public function setPrice($price)
    {
        return $this->setTotalPrice($price);
    }

    public function addDiscountElement(OrderDiscountElement $element)
    {
        $this->discountElements[] = $element;
        $element->setIterator(count($this->discountElements) + 1);
        $element->setOrder($this);

        return $this;
    }

    /**
     * @return ArrayCollection|OrderDiscountElement[]
     */
    public function getDiscountElements()
    {
        return $this->discountElements;
    }

    /**
     * @param OrderDiscountElement $element
     *
     * @return bool
     */
    public function removeDiscountElement(OrderDiscountElement $element)
    {
        return $this->discountElements->removeElement($element);
    }


    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return BaseOrder
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return array
     */
    public function getOrderRentElements()
    {
        $elements = [];
        foreach ($this->getOrderElements() as $element) {
            if ($element->getProduct() instanceof RentableProduct) {
                $elements[] = $element;
            }
        }

        return $elements;
    }

    public function addOrderRentElement(OrderElement $orderElement)
    {
        return $this->addOrderElement($orderElement);
    }

    public function removeOrderRentElement(OrderElement $orderElement)
    {
        return $this->removeOrderElement($orderElement);
    }

    /**
     * @return ArrayCollection|Shipment[]
     */
    public function getShipments()
    {
        return $this->shipments;
    }

    /**
     * @param Shipment $shipment
     *
     * @return BaseOrder
     */
    public function addShipment(Shipment $shipment)
    {
        $this->shipments[] = $shipment;
        $shipment->setOrder($this);
        if ($this->getShippingMethod()) {
            $shipment->setShippingMethod($this->getShippingMethod());
        }
        $this->mustRecalculShipments = true;

        return $this;
    }

    public function removeShipment(Shipment $shipment)
    {
        $this->shipments->removeElement($shipment);
        $this->mustRecalculShipments = true;
    }

    public function setShipments($shipments)
    {
        $this->shipments = $shipments;
        $this->mustRecalculShipments = true;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getShippingMethod()
    {
        return $this->shippingMethod;
    }

    /**
     * @param mixed $shippingMethod
     *
     * @return BaseOrder
     */
    public function setShippingMethod(ShippingMethod $shippingMethod)
    {
        $this->shippingMethod = $shippingMethod;
        foreach ($this->getShipments() as $shipment) {
            $shipment->setShippingMethod($this->getShippingMethod());
        }
        $this->calculateShippingCost();
        $this->forcePriceCalculation();

        return $this;
    }

    public $justGotInvoice = false;

    //TODO savoir quand on a besoin de valider, est-ce que c'est dès qu'on a un paiement, ou que la commande passe en active?
    public function isElligibleForInvoice()
    {
        if ($this->getInvoiceReference() || !$this->getBillingAddress() || !$this->getShippingAddress() || !$this->getBillingAddress()->getCountry() || !$this->getShippingAddress()->getCountry()) {
            return false;
        }

        $to = [self::STATE_IN_PROGRESS, self::STATE_READY_FOR_SHIPPING, self::STATE_IN_SHIPPING, self::STATE_FINALIZED];

        if (!in_array($this->getState(), $to, true)) {
            return false;
        }

        //TODO gérer paiement

        return true;
    }

    public function mustRecalculShipments()
    {
        $return = $this->mustRecalculShipments;
        if (!$return && count($this->getShipments()) === 0 && count($this->getOrderElements()) > 0) {
            return true;
        }

        return $this->mustRecalculShipments;
    }

    public function recalculShipmentsFinished()
    {
        $this->mustRecalculShipments = false;

        return $this;
    }

    /**
     * @return ArrayCollection|Payment[]
     */
    public function getPayments()
    {
        return $this->payments;
    }

    /**
     * @param Payment $payment
     *
     * @return $this
     */
    public function addPayment(Payment $payment)
    {
        $this->payments->add($payment);
        $payment->setOrder($this);

        return $this;
    }

    /**
     * @param PaymentInterface $payment
     *
     * @return $this
     */
    public function removePayment(PaymentInterface $payment)
    {
        $this->payments->removeElement($payment);

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentStatus() : string
    {
        return $this->paymentStatus;
    }

    /**
     * @param string $paymentStatus
     *
     * @return BaseOrder
     */
    public function setPaymentStatus(string $paymentStatus) : BaseOrder
    {
        $this->paymentStatus = $paymentStatus;

        return $this;
    }

    /**
     * @return bool
     */
    public function isMustRecalculShipments() : bool
    {
        return $this->mustRecalculShipments;
    }

    /**
     * @param bool $mustRecalculShipments
     *
     * @return BaseOrder
     */
    public function setMustRecalculShipments(bool $mustRecalculShipments) : BaseOrder
    {
        $this->mustRecalculShipments = $mustRecalculShipments;

        return $this;
    }


}