<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Api\Model;

/**
 * Representation of a Klarna customer as an object.
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class Customer
{
    /** @var null|string */
    public $dateOfBirth;

    /** @var null|string */
    public $nationalIdentificationNumber;

    /** @var null|string */
    public $title;

    /** @var null|string */
    public $gender;

    /** @var null|string */
    public $lastFourSsn;

    /** @var null|string */
    public $type;

    /** @var null|string */
    public $vatId;

    /** @var null|string */
    public $organizationRegistrationId;

    /** @var null|string */
    public $organizationEntityType;
}
