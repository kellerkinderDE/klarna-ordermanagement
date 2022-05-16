<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Subscriber\Controller\Backend;

use Enlight\Event\SubscriberInterface;

/**
 * Subscriber to register controller.
 *
 * @author  Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class RegisterController implements SubscriberInterface
{
    /** @var string */
    protected $controllersDir;

    /**
     * @param string $controllersDir
     */
    public function __construct($controllersDir)
    {
        $this->controllersDir = $controllersDir;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_BestitOrderManagement' => [
                'registerBestitOrderManagementController',
            ],
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_BestitKlarnaPluginConfig' => [
                'registerBestitKlarnaPluginConfigController',
            ],
        ];
    }

    public function registerBestitOrderManagementController(): string
    {
        return "{$this->controllersDir}/Backend/BestitOrderManagement.php";
    }

    public function registerBestitKlarnaPluginConfigController(): string
    {
        return "{$this->controllersDir}/Backend/BestitKlarnaPluginConfig.php";
    }
}
