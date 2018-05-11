<?php

namespace BestitKlarnaOrderManagement\Components\Api\Model;

/**
 * Representation of a Klarna shipping address as an object.
 *
 * @package BestitKlarnaOrderManagement\Components\Api\Model
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class ShippingAddress
{
    /** @var string */
    public $givenName;

    /** @var string */
    public $familyName;

    /** @var string */
    public $title;

    /** @var string */
    public $streetAddress;

    /** @var string */
    public $streetAddress2;

    /** @var string */
    public $postalCode;

    /** @var string */
    public $city;

    /** @var string */
    public $region;

    /** @var string */
    public $country;

    /** @var string */
    public $email;

    /** @var string */
    public $phone;
}
