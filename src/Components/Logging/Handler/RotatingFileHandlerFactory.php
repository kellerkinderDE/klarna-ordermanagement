<?php

namespace BestitKlarnaOrderManagement\Components\Logging\Handler;

use BestitKlarnaOrderManagement\Components\ConfigReader;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

/**
 * RotatingFileHandler factory to include the Config reader in order to get the log expire date
 *
 * @author Senan Sharhan <senan.sharhan@bestit-online.de>
 */
class RotatingFileHandlerFactory
{
    /**
     * @param $filename
     * @param int  $maxFilesDefault
     * @param int  $level
     * @param bool $bubble
     * @param null $filePermission
     * @param bool $useLocking
     */
    public static function create(
        ConfigReader $configReader,
        $filename,
        $maxFilesDefault = 0,
        $level = Logger::DEBUG,
        $bubble = true,
        $filePermission = null,
        $useLocking = false
    ): RotatingFileHandler {
        return new RotatingFileHandler(
            $filename,
            $configReader->get('log_expire_time', $maxFilesDefault),
            $level,
            $bubble,
            $filePermission,
            $useLocking
        );
    }
}
