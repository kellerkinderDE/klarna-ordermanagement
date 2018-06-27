<?php

namespace BestitKlarnaOrderManagement\Subscriber\Controller\Backend;

use Enlight\Event\SubscriberInterface;
use Enlight_Controller_Action;
use Enlight_Controller_ActionEventArgs;
use Enlight_Hook_HookArgs;

/**
 * Subscriber to register controller.
 *
 * @package BestitKlarnaOrderManagement\Subscriber\Controller\Backend
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

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_BestitOrderManagement' => [
                'registerBestitOrderManagementController'
            ],
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_BestitKlarnaPluginConfig' => [
                'registerBestitKlarnaPluginConfigController'
            ],
        ];
    }

    /**
     * @return string
     */
    public function registerBestitOrderManagementController()
    {
        return "{$this->controllersDir}/Backend/BestitOrderManagement.php";
    }

    /**
     * @return string
     */
    public function registerBestitKlarnaPluginConfigController()
    {
        return "{$this->controllersDir}/Backend/BestitKlarnaPluginConfig.php";
    }
}
