<?php

namespace Dywee\OrderBundle\Entity;

use Dywee\CoreBundle\Model\AddressInterface;
use Dywee\CoreBundle\Model\CustomerInterface;
use Dywee\CoreBundle\Model\PersistableInterface;
use Dywee\ProductBundle\Entity\BaseProduct;
use Payum\Core\Model\PaymentInterface;

interface BaseOrderInterface extends PersistableInterface
{
    const STATE_IN_SESSION = 'order.state.session';
    const STATE_CANCELLED = 'order.state.cancelled';
    const STATE_WAITING = 'order.state.waiting';
    const STATE_IN_PROGRESS = 'order.state.in_progress';
    const STATE_FINALIZED = 'order.state.finalized';
    const STATE_RETURNED = 'order.state.returned';

    const STATE_READY_FOR_SHIPPING = 'order.state.ready_shipping';
    const STATE_IN_SHIPPING = 'order.state.in_shipping';
    const STATE_SHIPPED = 'order.state.shipped';

    const STATE_CUSTOMER_ERROR = 'order.state.customer_error';
    const STATE_SELLER_ERROR = 'order.state.seller_customer';
    const STATE_ERROR = 'order.state.error';

    const PAYMENT_STATUS_NONE = 'order_payment.state.waiting';
    const PAYMENT_STATUS_WAITING = 'order_payment.state.waiting';
    /** @deprecated */
    const PAYMENT_STATE_WAITING = 'order_payment.state.waiting';
    const PAYMENT_WAITING_VALIDATION = 'order_payment.state.waiting_validation';
    const PAYMENT_VALIDATED = 'order_payment.state.validated';
    const PAYMENT_REFUND = 'order_payment.state.refund';
    const PAYMENT_PARTIALLY_PAID = 'order_payment.state.partially_paid';
    const PAYMENT_AUTHORIZED = 'order_payment.state.authorized';
    const PAYMENT_CANCELLED = 'order_payment.state.cancelled';

    const TYPE_ONLY_BUY = 'order.type.buy';
    const TYPE_ONLY_RENT = 'order.type.rent';
    const TYPE_BUY_AND_RENT = 'order.type.both';

    /**
     * Set isGift
     *
     * @param boolean $isGift
     * @return BaseOrderInterface
     */
    public function setIsGift($isGift);

    /**
     * Get isGift
     *
     * @return boolean
     */
    public function isGift();

    /**
     * Set priceVatExcl
     *
     * @param float $priceVatExcl
     * @return BaseOrderInterface
     */
    public function setPriceVatExcl($priceVatExcl);

    /**
     * Get priceVatExcl
     *
     * @return float
     */
    public function getPriceVatExcl();

    /**
     * Set deliveryCost
     *
     * @param float $deliveryCost
     * @return BaseOrderInterface
     */
    public function setShippingCost($deliveryCost);

    /**
     * Get deliveryCost
     *
     * @return float
     */
    public function getShippingCost();

    /**
     * Set vatRate
     *
     * @param string $vatRate
     * @return BaseOrderInterface
     */
    public function setVatRate($vatRate);

    /**
     * Get vatRate
     *
     * @return string
     */
    public function getVatRate();

    /**
     * Set vatPrice
     *
     * @param float $vatPrice
     * @return BaseOrderInterface
     */
    public function setVatPrice($vatPrice);

    /**
     * Get vatPrice
     *
     * @return float
     */
    public function getVatPrice();

    /**
     * Set discountCode
     *
     * @param string $discountCode
     * @return BaseOrderInterface
     */
    public function setDiscountCode($discountCode);

    /**
     * Get discountCode
     *
     * @return string
     */
    public function getDiscountCode();

    /**
     * Set discountRate
     *
     * @param float $discountRate
     * @return BaseOrderInterface
     */
    public function setDiscountRate($discountRate);

    /**
     * Get discountRate
     *
     * @return float
     */
    public function getDiscountRate();

    /**
     * Set priceVatIncl
     *
     * @param float $priceVatIncl
     * @return BaseOrderInterface
     */
    public function setPriceVatIncl($priceVatIncl);

    /**
     * Get priceVatIncl
     *
     * @return float
     */
    public function getPriceVatIncl();

    /**
     * Set totalPrice
     *
     * @param float $totalPrice
     * @return BaseOrderInterface
     */
    public function setTotalPrice($totalPrice);

    /**
     * Get totalPrice
     *
     * @return float
     */
    public function getTotalPrice();

    /**
     * Set description
     *
     * @param string $description
     * @return BaseOrderInterface
     */
    public function setDescription($description);

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription();

    /**
     * Set createdAt
     *
     * @param \DateTime $creationDate
     * @return BaseOrderInterface
     */
    public function setCreatedAt(\DateTime $creationDate);

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * Set updatedAt
     *
     * @param \DateTime $updateDate
     * @return BaseOrderInterface
     */
    public function setUpdatedAt(\DateTime $updateDate);

    /**
     * Set validatedAt
     *
     * @param \DateTime $validationDate
     * @return BaseOrderInterface
     */
    public function setValidatedAt(\DateTime $validationDate);

    /**
     *
     * @return \DateTime
     */
    public function getValidatedAt();

    public function getUpdatedAt();

    /**
     * Set reference
     *
     * @param string $reference
     * @return BaseOrderInterface
     */
    public function setReference($reference);

    /**
     * Get reference
     *
     * @return string
     */
    public function getReference();

    /**
     * Set invoiceReference
     *
     * @param string $invoiceReference
     * @return BaseOrderInterface
     */
    public function setInvoiceReference($invoiceReference);

    /**
     * Get invoiceReference
     *
     * @return string
     */
    public function getInvoiceReference();

    /**
     * Set shippingMessage
     *
     * @param string $shippingMessage
     * @return BaseOrderInterface
     */
    public function setShippingMessage($shippingMessage);

    /**
     * Get shippingMessage
     *
     * @return string
     */
    public function getShippingMessage();

    /**
     * Set locale
     *
     * @param string $locale
     * @return BaseOrderInterface
     */
    public function setLocale($locale);

    /**
     * Get locale
     *
     * @return string
     */
    public function getLocale();

    /**
     * Set active
     *
     * @param boolean $active
     * @return BaseOrderInterface
     */
    public function setActive($active);

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive();

    /**
     * Set state
     *
     * @param integer $state
     * @return BaseOrderInterface
     */
    public function setState($state);

    /**
     * Get state
     *
     * @return integer
     */
    public function getState();

    /**
     * Set billingUser
     *
     * @param CustomerInterface $billingUser
     *
     * @return BaseOrderInterface
     */
    public function setBillingUser(CustomerInterface $billingUser = null);

    /**
     * Get billingUser
     *
     * @return CustomerInterface
     * */
    public function getBillingUser();

    /**
     * Set shippingUser
     *
     * @param CustomerInterface $shippingUser
     *
     * @return BaseOrderInterface
     */
    public function setShippingUser(CustomerInterface $shippingUser = null);

    /**
     * Get shippingUser
     *
     * @return CustomerInterface
     */
    public function getShippingUser();

    /**
     * Set billingAddress
     *
     * @param AddressInterface $billingAddress
     * @return BaseOrderInterface
     */
    public function setBillingAddress(AddressInterface $billingAddress = null);

    /**
     * Get billingAddress
     *
     * @return AddressInterface
     */
    public function getBillingAddress();

    /**
     * Set shippingAddress
     *
     * @param AddressInterface $shippingAddress
     * @return BaseOrderInterface
     */
    public function setShippingAddress(AddressInterface $shippingAddress = null);

    /**
     * Get shippingAddress
     *
     * @return AddressInterface
     */
    public function getShippingAddress();

    /**
     * Set discountValue
     *
     * @param float $discountValue
     * @return BaseOrderInterface
     */
    public function setDiscountValue($discountValue);

    /**
     * Get discountValue
     *
     * @return float
     */
    public function getDiscountValue();

    /**
     * Add orderElements
     *
     * @param \Dywee\OrderBundle\Entity\OrderElement $orderElements
     * @return BaseOrderInterface
     */
    public function addOrderElement(OrderElement $orderElements);

    /**
     * Remove orderElements
     *
     * @param OrderElement $orderElements
     */
    public function removeOrderElement(OrderElement $orderElements);

    /**
     * Get orderElements
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrderElements();


    public function countProducts($type = null);

    public function forcePriceCalculation();

    /**
     * @param BaseProduct $product
     * @param int $quantity
     * @param int $locationCoeff
     * @return BaseProduct
     */
    public function addProduct(BaseProduct $product, $quantity, $locationCoeff = 1);

    /**
     * @param BaseProduct $product
     * @return int
     */
    public function getQuantityForProduct(BaseProduct $product);


    /**
     * Get weight
     *
     * @return float
     */
    public function getWeight($byType = false);

    /**
     * @return $this
     */
    public function weightCalculation();


    public function removeProduct($product);


    public function containsElementReduction();

    /**
     * Set deposit
     *
     * @param float $deposit
     * @return BaseOrderInterface
     */
    public function setDeposit($deposit);

    /**
     * Get deposit
     *
     * @return float
     */
    public function getDeposit();

    /**
     * Set endedAt
     *
     * @param \DateTime $endingDate
     * @return BaseOrderInterface
     */
    public function setEndedAt(\DateTime $endingDate);

    /**
     * @return \DateTime
     */
    public function getEndedAt();

    /**
     * @param integer $duration
     * @return BaseOrderInterface
     */
    public function setDuration($duration);

    public function containsOnlyOneType($type = null);

    /**
     * Set isPriceTTC
     *
     * @param boolean $isPriceTTC
     *
     * @return BaseOrderInterface
     */
    public function setIsPriceTTC($isPriceTTC);

    /**
     * Get isPriceTTC
     *
     * @return boolean
     */
    public function getIsPriceTTC();


    /**
     * Set mailStep
     *
     * @param integer $mailStep
     *
     * @return BaseOrderInterface
     */
    public function setMailStep($mailStep);

    /**
     * Get mailStep
     *
     * @return boolean
     */
    public function getMailStep();

    public function decreaseStock();

    public function refundStock();

    public function isVirtual($forceRecalcul = false);

    public function isOnlyVirtual($forceRecalcul = false);

    public function setPreviousState($state);

    public function getPreviousState();

    /**
     * alias
     */
    public function getPrice();

    /**
     * alias
     * @param $price
     * @return $this
     */
    public function setPrice($price);


    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     * @return BaseOrder
     */
    public function setType($type);

    /**
     * @return PaymentInterface
     */
    public function getPayments() : PaymentInterface;

    /**
     * @param Payment $payment
     *
     * @return $this
     */
    public function addPayment(Payment $payment);

    /**
     * @param PaymentInterface $payment
     *
     * @return $this
     */
    public function removePayment(PaymentInterface $payment);

    /**
     * @return string
     */
    public function getPaymentStatus() : string;

    /**
     * @param string $paymentStatus
     *
     * @return BaseOrder
     */
    public function setPaymentStatus(string $paymentStatus) : BaseOrder;
}