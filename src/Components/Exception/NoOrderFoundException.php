<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Exception;

use RuntimeException;

/**
 * This exception will be thrown if the order is not found.
 *
 * @author Senan Sharhan <senan.sharhan@bestit-online.de>
 */
class NoOrderFoundException extends RuntimeException
{
}
