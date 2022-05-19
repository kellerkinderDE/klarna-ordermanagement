<?php

namespace BestitKlarnaOrderManagement\Components\Api\Model;

/**
 * Representation of a Klarna shipping address as an object.
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class ShippingAddress
{
    /** @var string */
    public $givenName;

    /** @var string */
    public $familyName;

    /** @var null|string */
    public $title;

    /** @var string */
    public $streetAddress;

    /** @var null|string */
    public $streetAddress2;

    /** @var string */
    public $postalCode;

    /** @var string */
    public $city;

    /** @var null|string */
    public $region;

    /** @var string */
    public $country;

    /** @var string */
    public $email;

    /** @var null|string */
    public $phone;

    /** @var null|string */
    public $organizationName;
}
