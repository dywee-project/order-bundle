<?php
/**
 * Created by PhpStorm.
 * User: Olivier
 * Date: 6/08/16
 * Time: 09:58
 */
namespace Dywee\OrderBundle\Entity;

use Dywee\AddressBundle\Entity\AddressInterface;
use Dywee\CoreBundle\Model\PersistableInterface;
use Dywee\ProductBundle\Entity\BaseProduct;
use Dywee\ShipmentBundle\Entity\Deliver;
use Dywee\ShipmentBundle\Entity\Shipment;
use Dywee\ShipmentBundle\Entity\ShipmentMethod;
use Dywee\UserBundle\Entity\User;

interface BaseOrderInterface extends PersistableInterface
{
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
    public function getIsGift();

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
    public function setDeliveryCost($deliveryCost);

    public function setShippingCost($deliveryCost);

    /**
     * Get deliveryCost
     *
     * @return float
     */
    public function getDeliveryCost();

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
     * Set deliver
     *
     * @param string $deliver
     * @return BaseOrderInterface
     */
    public function setDeliver($deliver);

    /**
     * Get deliver
     *
     * @return string
     */
    public function getDeliver();

    /**
     * Set deliveryMethod
     *
     * @param string $deliveryMethod
     * @return BaseOrderInterface
     */
    public function setDeliveryMethod($deliveryMethod);

    /**
     * Get deliveryMethod
     *
     * @return string
     */
    public function getDeliveryMethod();

    /**
     * Set deliveryInfo
     *
     * @param string $deliveryInfo
     * @return BaseOrderInterface
     */
    public function setDeliveryInfo($deliveryInfo);

    /**
     * Get deliveryInfo
     *
     * @return string
     */
    public function getDeliveryInfo();

    /**
     * Set paymentMethod
     *
     * @param string $paymentMethod
     * @return BaseOrderInterface
     */
    public function setPaymentMethod($paymentMethod);

    /**
     * Get paymentMethod
     *
     * @return string
     */
    public function getPayeentMethod();

    /**
     * Set paymentInfos
     *
     * @param string $paymentInfos
     * @return BaseOrderInterface
     */
    public function setPaymentInfos($paymentInfos);

    /**
     * Get paymentInfos
     *
     * @return string
     */
    public function getPaymentInfos();

    /**
     * Set paymentState
     *
     * @param string $paymentState
     * @return BaseOrderInterface
     */
    public function setPaymentState($paymentState);

    /**
     * Get paymentState
     *
     * @return string
     */
    public function getPaymentState();

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
     * @param \Dywee\UserBundle\Entity\User $billingUser
     * @return BaseOrderInterface
     */
    public function setBillingUser(User $billingUser = null);

    /**
     * Get billingUser
     *
     * @return \Dywee\UserBundle\Entity\User
     */
    public function getBillingUser();

    /**
     * Set shippingUser
     *
     * @param User $shippingUser
     * @return BaseOrderInterface
     */
    public function setShippingUser(User $shippingUser = null);

    /**
     * Get shippingUser
     *
     * @return \Dywee\UserBundle\Entity\User
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

    /**
     * Add shipments
     *
     * @param Shipment $shipment
     * @return BaseOrderInterface
     */
    public function addShipment(Shipment $shipment);

    /**
     * Remove shipments
     *
     * @param Shipment $shipments
     */
    public function removeShipment(Shipment $shipments);

    /**
     * Get shipments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getShipments();

    public function countProducts($type = null);

    public function forcePriceCalculation();

    /**
     * @param boolean $force
     */
    public function shipmentsCalculation($force = false);

    public function addProduct(BaseProduct $product, $quantity, $locationCoeff = 1);

    /**
     * @param BaseProduct $product
     * @return int
     */
    public function getQuantityForProduct(BaseProduct $product);

    /**
     * Set weight
     *
     * @param float $weight
     * @return BaseOrderInterface
     */
    public function setWeight($weight);

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

    /**
     * Set shippingMethod
     *
     * @param ShipmentMethod $shippingMethod
     * @return BaseOrderInterface
     */
    public function setShippingMethod(ShipmentMethod $shippingMethod = null);

    /**
     * Get shippingMethod
     *
     * @return ShipmentMethod
     */
    public function getShippingMethod();

    public function removeProduct($product);

    /**
     * Add deliver
     *
     * @param Deliver $deliver
     * @return BaseOrderInterface
     */
    public function addDeliver(Deliver $deliver);

    /**
     * Remove deliver
     *
     * @param Deliver $deliver
     */
    public function removeDeliver(Deliver $deliver);

    public function setFromOffer($offer);

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
    public function getReturningAt();
    public function getReturnedAt();

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
     * @return BaseOrderInterface
     */
    public function countSendedShipments();

    public function checkIfIsDone();

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

    public function getOldState();

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
     * @param \Datetime $returningAt
     * @return BaseOrder
     */
    public function setReturningAt($returningAt);

    /**
     * @param \Datetime $returnedAt
     * @return BaseOrder
     */

    public function setReturnedAt($returnedAt);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     * @return BaseOrder
     */
    public function setType($type);

}