<?php

namespace BestitKlarnaOrderManagement\Components\Api\Resource;

use BestitKlarnaOrderManagement\Components\Api\Model\Order as OrderModel;
use BestitKlarnaOrderManagement\Components\Api\Request;
use BestitKlarnaOrderManagement\Components\Api\Response;
use BestitKlarnaOrderManagement\Components\Api\ResponseWrapperTrait;
use BestitKlarnaOrderManagement\Components\Curl\Client;
use BestitKlarnaOrderManagement\Components\Curl\Exception\KlarnaCurlClientException;
use Symfony\Component\Serializer\SerializerInterface;

class Order
{
    use ResponseWrapperTrait;

    /** @var Client */
    protected $httpClient;

    /**
     * @param Client          $client
     * @param SerializerInterface $serializer
     */
    public function __construct(Client $client, SerializerInterface $serializer)
    {
        $this->httpClient = $client;
        $this->serializer = $serializer;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function get(Request $request)
    {
        $baseUrl = $request->getBaseUrl();
        $orderId = $request->getQueryParameter('order_id');

        try {
            $response = $this->httpClient->get(
                "{$baseUrl}ordermanagement/v1/orders/{$orderId}",
                [
                    'headers' => $request->getHeaders()
                ]
            );
        } catch (KlarnaCurlClientException $e) {
            return $this->wrapException($e);
        }

        return $this->wrapResponse($response, OrderModel::class);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function acknowledge(Request $request)
    {
        $baseUrl = $request->getBaseUrl();
        $orderId = $request->getQueryParameter('order_id');

        try {
            $response = $this->httpClient->post(
                "{$baseUrl}/ordermanagement/v1/orders/{$orderId}/acknowledge",
                [
                    'headers' => $request->getHeaders()
                ]
            );
        } catch (KlarnaCurlClientException $e) {
            return $this->wrapException($e);
        }

        return $this->wrapResponse($response);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function extendAuthTime(Request $request)
    {
        $baseUrl = $request->getBaseUrl();
        $orderId = $request->getQueryParameter('order_id');

        try {
            $response = $this->httpClient->post(
                "{$baseUrl}/ordermanagement/v1/orders/{$orderId}/extend-authorization-time",
                [
                    'headers' => $request->getHeaders()
                ]
            );
        } catch (KlarnaCurlClientException $e) {
            return $this->wrapException($e);
        }

        return $this->wrapResponse($response);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function releaseRemainingAmount(Request $request)
    {
        $baseUrl = $request->getBaseUrl();
        $orderId = $request->getQueryParameter('order_id');

        try {
            $response = $this->httpClient->post(
                "{$baseUrl}/ordermanagement/v1/orders/{$orderId}/release-remaining-authorization",
                [
                    'headers' => $request->getHeaders()
                ]
            );
        } catch (KlarnaCurlClientException $e) {
            return $this->wrapException($e);
        }

        return $this->wrapResponse($response);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function cancel(Request $request)
    {
        $baseUrl = $request->getBaseUrl();
        $orderId = $request->getQueryParameter('order_id');

        try {
            $response = $this->httpClient->post(
                "{$baseUrl}/ordermanagement/v1/orders/{$orderId}/cancel",
                [
                    'headers' => $request->getHeaders()
                ]
            );
        } catch (KlarnaCurlClientException $e) {
            return $this->wrapException($e);
        }

        return $this->wrapResponse($response);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function updateAddress(Request $request)
    {
        $baseUrl = $request->getBaseUrl();
        $orderId = $request->getQueryParameter('order_id');

        try {
            $response = $this->httpClient->patch(
                "{$baseUrl}/ordermanagement/v1/orders/{$orderId}/customer-details",
                [
                    'json' => $request->getPayload(),
                    'headers' => $request->getHeaders()
                ]
            );
        } catch (KlarnaCurlClientException $e) {
            return $this->wrapException($e);
        }

        return $this->wrapResponse($response);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function updateOrder(Request $request)
    {
        $baseUrl = $request->getBaseUrl();
        $orderId = $request->getQueryParameter('order_id');

        try {
            $response = $this->httpClient->patch(
                "{$baseUrl}/ordermanagement/v1/orders/{$orderId}/authorization",
                [
                    'json' => $request->getPayload(),
                    'headers' => $request->getHeaders()
                ]
            );
        } catch (KlarnaCurlClientException $e) {
            return $this->wrapException($e);
        }

        return $this->wrapResponse($response);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function updateMerchantReferences(Request $request)
    {
        $baseUrl = $request->getBaseUrl();
        $orderId = $request->getQueryParameter('order_id');

        try {
            $response = $this->httpClient->patch(
                "{$baseUrl}/ordermanagement/v1/orders/{$orderId}/merchant-references",
                [
                    'json' => $request->getPayload(),
                    'headers' => $request->getHeaders()
                ]
            );
        } catch (KlarnaCurlClientException $e) {
            return $this->wrapException($e);
        }

        return $this->wrapResponse($response);
    }
}
