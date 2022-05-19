<?php

namespace BestitKlarnaOrderManagement\Components;

use BestitKlarnaOrderManagement\Components\DependencyInjection\DependencyInjectionExtension;

/**
 * This class includes all Constants that are used in Order Management
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 * @author Senan Sharhan <senan.sharhan@bestit-online.de>
 */
class Constants
{
    public const LIVE_API = 'https://api.klarna.com/';
    public const TEST_API = 'https://api.playground.klarna.com/';

    public const CAPTURE_CREATE_URI = '';

    /** constant which includes the dependency to Order Management */
    public const DEPENDENCY_INJECTION_EXTENSIONS = [
        DependencyInjectionExtension::class,
    ];

    public const SHIPPING_COSTS_REFERENCE = 'SHIPPING_COSTS';

    /**
     * List of all Klarna order Lines Types
     */
    public const KLARNA_LINE_ITEM_TYPE_PHYSICAL     = 'physical';
    public const KLARNA_LINE_ITEM_TYPE_DISCOUNT     = 'discount';
    public const KLARNA_LINE_ITEM_TYPE_SURCHARGE    = 'surcharge';
    public const KLARNA_LINE_ITEM_TYPE_SHIPPING_FEE = 'shipping_fee';
    public const KLARNA_LINE_ITEM_TYPE_SALES_TAX    = 'sales_tax';
    public const KLARNA_LINE_ITEM_TYPE_STORE_CREDIT = 'store_credit';
    public const KLARNA_LINE_ITEM_TYPE_GIFT_CARD    = 'gift_card';
    public const KLARNA_LINE_ITEM_TYPE_DIGITAL      = 'digital';

    /**
     * List of all shopware article mode
     */
    public const SHOPWARE_PRODUCT_MODE            = 0;
    public const SHOPWARE_PREMIUM_PRODUCT_MODE    = 1;
    public const SHOPWARE_VOUCHER_MODE            = 2;
    public const SHOPWARE_REBATE_MODE             = 3;
    public const SHOPWARE_SURCHARGE_DISCOUNT_MODE = 4;
    public const SHOPWARE_BUNDLE_MODE             = 10;

    /**
     * Transaction logger actions
     */
    public const UPDATE_ORDER_ACTION             = 'update_order';
    public const CANCEL_ORDER_ACTION             = 'cancel_order';
    public const EXTEND_AUTH_TIME_ACTION         = 'extend_auth_time';
    public const RELEASE_REMAINING_AMOUNT_ACTION = 'release_remaining_amount';
    public const CREATE_CAPTURE_ACTION           = 'create_capture';
    public const CREATE_REFUND_ACTION            = 'create_refund';

    /**
     * Supported External Checkout by Klarna
     */
    public const SUPPORTED_EXTERNAL_CHECKOUT = ['SwagPaymentPaypal', 'SwagPaymentPayPalUnified', 'BestitAmazonPay'];

    /**
     * List of shopware custom products plugin modes
     */
    public const CUSTOM_PRODUCT_PRODUCT = '1';
    public const CUSTOM_PRODUCT_OPTION  = '2';
    public const CUSTOM_PRODUCT_VALUE   = '3';

    public const CUSTOMER_ORGANIZATION_TYPE = 'organization';
    public const CUSTOMER_PRIVATE_TYPE      = 'person';
}
