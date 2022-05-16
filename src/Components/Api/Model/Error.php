<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Api\Model;

/**
 * Representation of a Klarna error as an object.
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class Error
{
    /** @var int|string */
    public $errorCode = 0;

    /** @var string[] */
    public $errorMessages = ['Could not parse error'];

    /** @var null|string */
    public $correlationId;
}
