<?php

namespace BestitKlarnaOrderManagement\Components;

use BestitKlarnaOrderManagement\Components\DependencyInjection\DependencyInjectionExtension;

/**
 * This class includes all Constants that are used in Order Management
 *
 * @package BestitKlarnaOrderManagement\Components
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 * @author Senan Sharhan <senan.sharhan@bestit-online.de>
 */
class Constants
{
    const LIVE_API = 'https://api.klarna.com/';
    const TEST_API = 'https://api.playground.klarna.com/';

    const CAPTURE_CREATE_URI = '';

    /** constant which includes the dependency to Order Management */
    const DEPENDENCY_INJECTION_EXTENSIONS = [
        DependencyInjectionExtension::class
    ];

    const SHIPPING_COSTS_REFERENCE = 'SHIPPING_COSTS';

    /**
     * List of all Klarna order Lines Types
     */
    const KLARNA_LINE_ITEM_TYPE_PHYSICAL = 'physical';
    const KLARNA_LINE_ITEM_TYPE_DISCOUNT = 'discount';
    const KLARNA_LINE_ITEM_TYPE_SURCHARGE = 'surcharge';
    const KLARNA_LINE_ITEM_TYPE_SHIPPING_FEE = 'shipping_fee';
    const KLARNA_LINE_ITEM_TYPE_SALES_TAX = 'sales_tax';
    const KLARNA_LINE_ITEM_TYPE_STORE_CREDIT = 'store_credit';
    const KLARNA_LINE_ITEM_TYPE_GIFT_CARD = 'gift_card';
    const KLARNA_LINE_ITEM_TYPE_DIGITAL = 'digital';

    /**
     * List of all shopware article mode
     */
    const SHOPWARE_PRODUCT_MODE = 0;
    const SHOPWARE_PREMIUM_PRODUCT_MODE = 1;
    const SHOPWARE_VOUCHER_MODE = 2;
    const SHOPWARE_REBATE_MODE = 3;
    const SHOPWARE_SURCHARGE_DISCOUNT_MODE = 4;
    const SHOPWARE_BUNDLE_MODE = 10;

    /**
     * Transaction logger actions
     */
    const UPDATE_ORDER_ACTION = 'update_order';
    const CANCEL_ORDER_ACTION = 'cancel_order';
    const EXTEND_AUTH_TIME_ACTION = 'extend_auth_time';
    const RELEASE_REMAINING_AMOUNT_ACTION = 'release_remaining_amount';
    const CREATE_CAPTURE_ACTION = 'create_capture';
    const CREATE_REFUND_ACTION = 'create_refund';

    /**
     * Supported External Checkout by Klarna
     */
    const SUPPORTED_EXTERNAL_CHECKOUT = ['SwagPaymentPaypal', 'BestitAmazonPay'];
}
