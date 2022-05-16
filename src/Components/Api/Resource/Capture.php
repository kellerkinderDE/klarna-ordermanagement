<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Api\Resource;

use BestitKlarnaOrderManagement\Components\Api\Request;
use BestitKlarnaOrderManagement\Components\Api\Response;
use BestitKlarnaOrderManagement\Components\Api\ResponseWrapperTrait;
use BestitKlarnaOrderManagement\Components\Curl\Client;
use BestitKlarnaOrderManagement\Components\Curl\Exception\KlarnaCurlClientException;
use Symfony\Component\Serializer\SerializerInterface;

class Capture
{
    use ResponseWrapperTrait;

    /** @var Client */
    protected $httpClient;

    public function __construct(Client $client, SerializerInterface $serializer)
    {
        $this->httpClient = $client;
        $this->serializer = $serializer;
    }

    public function create(Request $request): Response
    {
        $baseUrl = $request->getBaseUrl();
        $orderId = $request->getQueryParameter('order_id');

        try {
            $response = $this->httpClient->post(
                "{$baseUrl}/ordermanagement/v1/orders/{$orderId}/captures",
                [
                    'json'    => $request->getPayload(),
                    'headers' => $request->getHeaders(),
                ]
            );
        } catch (KlarnaCurlClientException $e) {
            return $this->wrapException($e);
        }

        return $this->wrapResponse($response);
    }

    public function resend(Request $request): Response
    {
        $baseUrl   = $request->getBaseUrl();
        $orderId   = $request->getQueryParameter('order_id');
        $captureId = $request->getQueryParameter('capture_id');

        try {
            $response = $this->httpClient->post(
                "{$baseUrl}/ordermanagement/v1/orders/{$orderId}/captures/{$captureId}/trigger-send-out",
                [
                    'headers' => $request->getHeaders(),
                ]
            );
        } catch (KlarnaCurlClientException $e) {
            return $this->wrapException($e);
        }

        return $this->wrapResponse($response);
    }

    public function updateShippingInfo(Request $request): Response
    {
        $baseUrl   = $request->getBaseUrl();
        $orderId   = $request->getQueryParameter('order_id');
        $captureId = $request->getQueryParameter('capture_id');

        try {
            $response = $this->httpClient->post(
                "{$baseUrl}/ordermanagement/v1/orders/{$orderId}/captures/{$captureId}/shipping-info",
                [
                    'json'    => $request->getPayload(),
                    'headers' => $request->getHeaders(),
                ]
            );
        } catch (KlarnaCurlClientException $e) {
            return $this->wrapException($e);
        }

        return $this->wrapResponse($response);
    }
}
