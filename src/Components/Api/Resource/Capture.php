<?php

namespace BestitKlarnaOrderManagement\Components\Api\Resource;

use BestitKlarnaOrderManagement\Components\Api\Request;
use BestitKlarnaOrderManagement\Components\Api\Response;
use BestitKlarnaOrderManagement\Components\Api\ResponseWrapperTrait;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Interface to interact with Klarna capture(s).
 *
 * @package BestitKlarnaOrderManagement\Components\Api\Resource
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class Capture
{
    use ResponseWrapperTrait;

    /** @var HttpClient */
    protected $httpClient;

    /**
     * @param HttpClient $client
     * @param SerializerInterface $serializer
     */
    public function __construct(HttpClient $client, SerializerInterface $serializer)
    {
        $this->httpClient = $client;
        $this->serializer = $serializer;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function create(Request $request)
    {
        $baseUrl = $request->getBaseUrl();
        $orderId = $request->getQueryParameter('order_id');

        try {
            $response = $this->httpClient->post(
                "{$baseUrl}/ordermanagement/v1/orders/{$orderId}/captures",
                [
                    'json' => $request->getPayload(),
                    'headers' => $request->getHeaders()
                ]
            );
        } catch (RequestException $e) {
            return $this->wrapGuzzleException($e);
        }

        return $this->wrapGuzzleResponse($response);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function resend(Request $request)
    {
        $baseUrl = $request->getBaseUrl();
        $orderId = $request->getQueryParameter('order_id');
        $captureId = $request->getQueryParameter('capture_id');

        try {
            $response = $this->httpClient->post(
                "{$baseUrl}/ordermanagement/v1/orders/{$orderId}/captures/{$captureId}/trigger-send-out",
                [
                    'headers' => $request->getHeaders()
                ]
            );
        } catch (RequestException $e) {
            return $this->wrapGuzzleException($e);
        }

        return $this->wrapGuzzleResponse($response);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function updateShippingInfo(Request $request)
    {
        $baseUrl = $request->getBaseUrl();
        $orderId = $request->getQueryParameter('order_id');
        $captureId = $request->getQueryParameter('capture_id');

        try {
            $response = $this->httpClient->post(
                "{$baseUrl}/ordermanagement/v1/orders/{$orderId}/captures/{$captureId}/shipping-info",
                [
                    'json' => $request->getPayload(),
                    'headers' => $request->getHeaders()
                ]
            );
        } catch (RequestException $e) {
            return $this->wrapGuzzleException($e);
        }

        return $this->wrapGuzzleResponse($response);
    }
}
