<?php

namespace Dywee\OrderBundle\Model\MondialRelayShippingStatus;

class MondialRelayShippingStatus
{
    const IN_RELAY = 'shipping_status.mondial_relay.relay';
    const RETURNED_DELIVER = 'shipping_status.mondial_relay.returned_deliver';
    const RETURNED_BUYER = 'shipping_status.mondial_relay.returned_buyer';
    const REGISTERED = 'shipping_status.mondial_relay.registered';
    const SHIPPING = 'shipping_status.mondial_relay.shipping';
    const ARRIVED = 'shipping_status.mondial_relay.arrived';
    const ERROR = 'shipping_status.mondial_relay.error';

}