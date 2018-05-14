<?php

namespace BestitKlarnaOrderManagement\Components\Api\Resource;

use BestitKlarnaOrderManagement\Components\Api\Model\Order as OrderModel;
use BestitKlarnaOrderManagement\Components\Api\Request;
use BestitKlarnaOrderManagement\Components\Api\Response;
use BestitKlarnaOrderManagement\Components\Api\ResponseWrapperTrait;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Interface to interact with Klarna order(s).
 *
 * @package BestitKlarnaOrderManagement\Components\Api\Resource
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class Order
{
    use ResponseWrapperTrait;

    /** @var HttpClient */
    protected $httpClient;

    /**
     * @param HttpClient          $client
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
    public function get(Request $request)
    {
        $baseUrl = $request->getBaseUrl();
        $orderId = $request->getQueryParameter('order_id');

        try {
            $response = $this->httpClient->get(
                "{$baseUrl}/ordermanagement/v1/orders/{$orderId}",
                [
                    'headers' => $request->getHeaders()
                ]
            );
        } catch (RequestException $e) {
            return $this->wrapGuzzleException($e);
        }

        return $this->wrapGuzzleResponse($response, OrderModel::class);
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
        } catch (RequestException $e) {
            return $this->wrapGuzzleException($e);
        }

        return $this->wrapGuzzleResponse($response);
    }
}
