<?php

namespace BestitKlarnaOrderManagement\Components\Api\Model;

/**
 * Representation of a Klarna customer as an object.
 *
 * @package BestitKlarnaOrderManagement\Components\Api\Model
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class Customer
{
    /** @var string|null */
    public $dateOfBirth;

    /** @var string|null */
    public $nationalIdentificationNumber;

    /** @var string|null */
    public $title;

    /** @var string|null */
    public $gender;

    /** @var string|null */
    public $lastFourSsn;

    /** @var string|null */
    public $type;

    /** @var string|null */
    public $vatId;

    /** @var string|null */
    public $organizationRegistrationId;

    /** @var string|null */
    public $organizationEntityType;
}
