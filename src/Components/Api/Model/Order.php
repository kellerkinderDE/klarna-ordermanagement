<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Api\Model;

use DateTime;

/**
 * Representation of a Klarna order as an object.
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class Order
{
    /** @var null|string */
    public $orderId;

    /** @var null|string */
    public $status;

    /** @var null|string */
    public $fraudStatus;

    /** @var int */
    public $orderAmount;

    /** @var null|int */
    public $orderTaxAmount;

    /** @var null|int */
    public $originalOrderAmount;

    /** @var null|int */
    public $capturedAmount;

    /** @var null|int */
    public $refundedAmount;

    /** @var null|int */
    public $remainingAuthorizedAmount;

    /** @var string */
    public $purchaseCurrency;

    /** @var string */
    public $locale;

    /** @var LineItem[] */
    public $orderLines;

    /** @var null|string */
    public $merchantReference1;

    /** @var null|string */
    public $merchantReference2;

    /** @var null|string */
    public $klarnaReference;

    /** @var null|Customer */
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

    /** @var null|array */
    public $merchantUrls;

    /** @var null|string */
    public $merchantData;

    /** @var null|MerchantFees */
    public $merchantFees;

    /** @var null|Attachment */
    public $attachment;

    /** @var Options */
    public $options;
}
