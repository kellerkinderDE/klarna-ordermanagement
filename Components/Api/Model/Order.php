<?php

namespace BestitKlarnaOrderManagement\Components\Api\Model;

use DateTime;

/**
 * Representation of a Klarna order as an object.
 *
 * @package BestitKlarnaOrderManagement\Components\Api\Model
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class Order
{
    /** @var string|null */
    public $orderId;

    /** @var string|null */
    public $status;

    /** @var string|null */
    public $fraudStatus;

    /** @var int */
    public $orderAmount;

    /** @var int|null */
    public $orderTaxAmount;

    /** @var int|null */
    public $originalOrderAmount;

    /** @var int|null */
    public $capturedAmount;

    /** @var int|null */
    public $refundedAmount;

    /** @var int|null */
    public $remainingAuthorizedAmount;

    /** @var string */
    public $purchaseCurrency;

    /** @var string */
    public $locale;

    /** @var LineItem[] */
    public $orderLines;

    /** @var string|null */
    public $merchantReference1;

    /** @var string|null */
    public $merchantReference2;

    /** @var string|null */
    public $klarnaReference;

    /** @var Customer|null */
    public $customer;

    /** @var BillingAddress */
    public $billingAddress;

    /** @var ShippingAddress */
    public $shippingAddress;

    /** @var DateTime */
    public $createdAt;

    /** @var string */
    public $purchaseCountry;

    /** @var DateTime */
    public $expiresAt;

    /** @var Capture[] */
    public $captures = [];

    /** @var Refund[] */
    public $refunds = [];

    /** @var array|null */
    public $merchantUrls;

    /** @var string|null */
    public $merchantData;

    /** @var MerchantFees|null */
    public $merchantFees;

    /** @var Attachment|null */
    public $attachment;

    /** @var Options */
    public $options;
}
