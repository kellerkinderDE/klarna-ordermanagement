<?php

namespace BestitKlarnaOrderManagement\Components\Exception;

use RuntimeException;

/**
 * This exception will be thrown if there is no supported calculator.
 * Since we provide a default PHP calculator this exception should
 * never be thrown unless there is a custom modification.
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class NoSupportedCalculatorException extends RuntimeException
{
}
