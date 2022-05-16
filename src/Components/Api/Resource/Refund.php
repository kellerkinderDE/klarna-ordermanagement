<?php

namespace BestitKlarnaOrderManagement\Components\Api\Resource;

use BestitKlarnaOrderManagement\Components\Api\Request;
use BestitKlarnaOrderManagement\Components\Api\Response;
use BestitKlarnaOrderManagement\Components\Api\ResponseWrapperTrait;
use BestitKlarnaOrderManagement\Components\Curl\Client;
use BestitKlarnaOrderManagement\Components\Curl\Exception\KlarnaCurlClientException;
use Symfony\Component\Serializer\SerializerInterface;

class Refund
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
    public function create(Request $request)
    {
        $baseUrl = $request->getBaseUrl();
        $orderId = $request->getQueryParameter('order_id');

        try {
            $response = $this->httpClient->post("{$baseUrl}/ordermanagement/v1/orders/{$orderId}/refunds", [
                'json' => $request->getPayload(),
                'headers' => $request->getHeaders()
            ]);
        } catch (KlarnaCurlClientException $e) {
            return $this->wrapException($e);
        }

        return $this->wrapResponse($response);
    }
}
