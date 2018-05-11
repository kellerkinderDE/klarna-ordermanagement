<?php

namespace BestitKlarnaOrderManagement\Components\Api\Model;

/**
 * Representation of a Klarna error as an object.
 *
 * @package BestitKlarnaOrderManagement\Components\Api\Model
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class Error
{
    /** @var string|int */
    public $errorCode;

    /** @var string[] */
    public $errorMessages;

    /** @var string|null */
    public $correlationId;
}
