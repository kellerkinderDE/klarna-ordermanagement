<?php

namespace BestitKlarnaOrderManagement\Components\Api\Resource;

use BestitKlarnaOrderManagement\Components\Api\Model\RecurringOrderResponse;
use BestitKlarnaOrderManagement\Components\Api\Request;
use BestitKlarnaOrderManagement\Components\Api\Response;
use BestitKlarnaOrderManagement\Components\Api\ResponseWrapperTrait;
use BestitKlarnaOrderManagement\Components\Curl\Client;
use BestitKlarnaOrderManagement\Components\Curl\Exception\KlarnaCurlClientException;
use Symfony\Component\Serializer\SerializerInterface;

class RecurringOrder
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
        $baseUrl       = $request->getBaseUrl();
        $customerToken = $request->getQueryParameter('customerToken');

        try {
            $response = $this->httpClient->post(
                "{$baseUrl}/customer-token/v1/tokens/{$customerToken}/order",
                [
                    'json'    => $request->getPayload(),
                    'headers' => $request->getHeaders(),
                ]
            );
        } catch (KlarnaCurlClientException $e) {
            return $this->wrapException($e);
        }

        return $this->wrapResponse($response, RecurringOrderResponse::class);
    }
}
