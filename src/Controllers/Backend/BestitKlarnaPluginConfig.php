<?php

use BestitKlarnaOrderManagement\Components\Api\Request;
use BestitKlarnaOrderManagement\Components\Api\Resource\Order as OrderResource;
use BestitKlarnaOrderManagement\Controllers\JsonableResponseTrait;
use BestitKlarnaOrderManagement\Components\Logging\ZipCreator;
use Shopware\Components\CSRFWhitelistAware;
use Symfony\Component\HttpFoundation\Response;
use BestitKlarnaOrderManagement\Components\Constants;

/**
 * Controller to handle some backend actions related to the config, such as test API credentials or download log files.
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 * @author Senan Sharhan <senan.sharhan@bestit-online.de>
 */
class Shopware_Controllers_Backend_BestitKlarnaPluginConfig extends Shopware_Controllers_Backend_ExtJs implements CSRFWhitelistAware
{
    use JsonableResponseTrait;

    /** @var Enlight_Controller_Plugins_ViewRenderer_Bootstrap */
    protected $viewRenderer;
    /** @var OrderResource */
    protected $orderResource;
    /** @var ZipCreator */
    protected $logZipCreator;

    /**
     * @return void
     */
    public function preDispatch()
    {
        $this->viewRenderer = $this->Front()->Plugins()->get('ViewRenderer');
        $this->viewRenderer->setNoRender();
        $this->orderResource = $this->get('bestit_klarna_order_management.components.api.resource.order');
        $this->logZipCreator = $this->get('bestit_klarna_order_management.components.logging.zip_creator');
    }

    /**
     * Tests the validity of the given credentials in the plugin config.
     *
     * @return void
     */
    public function testApiCredentialsAction()
    {
        $success = true;
        $liveMode = $this->Request()->getParam('liveMode');
        $merchantId = $this->Request()->getParam('merchantId');
        $merchantPass = $this->Request()->getParam('merchantPass');

        $request = new Request();
        $request->setBaseUrl($liveMode === 'true' ? Constants::LIVE_API : Constants::TEST_API);
        $request->addQueryParameter('order_id', 'DUMMY_ORDER_ID');

        $request->addHeader(
            'Authorization',
            'Basic ' . base64_encode("{$merchantId}:{$merchantPass}")
        );

        $response = $this->orderResource->get($request);

        if ($response->isError() && $response->getError()->errorCode === Response::HTTP_UNAUTHORIZED) {
            $success = false;
        }

        $this->jsonResponse([
            'success' => $success
        ]);
    }

    /**
     * Downloads all saved logs
     *
     * @return void
     */
    public function downloadLogsAction()
    {
        $zipName = 'bestit_klarna_logs_' . time() . '.zip';

        $this->logZipCreator->zipKlarnaLogFiles($zipName);

        $this->Response()->setHeader('Content-Type', 'application/zip', true);
        $this->Response()->setHeader('Content-Length', filesize($zipName), true);
        $this->Response()->setHeader('Content-Disposition', "attachment; filename='{$zipName}'", true);
        $this->Response()->setBody(file_get_contents($zipName));
    }

    /**
     * Returns a list with actions which should not be validated for CSRF protection
     *
     * @return string[]
     */
    public function getWhitelistedCSRFActions()
    {
        return [
            'testApiCredentials',
            'downloadLogs'
        ];
    }
}
