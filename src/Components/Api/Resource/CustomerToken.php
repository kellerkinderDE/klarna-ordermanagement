<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Api\Resource;

use BestitKlarnaOrderManagement\Components\Api\Model\CustomerTokenResponse;
use BestitKlarnaOrderManagement\Components\Api\Request;
use BestitKlarnaOrderManagement\Components\Api\Response;
use BestitKlarnaOrderManagement\Components\Api\ResponseWrapperTrait;
use BestitKlarnaOrderManagement\Components\Curl\Client;
use BestitKlarnaOrderManagement\Components\Curl\Exception\KlarnaCurlClientException;
use Symfony\Component\Serializer\SerializerInterface;

class CustomerToken
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
        $baseUrl   = $request->getBaseUrl();
        $authToken = $request->getQueryParameter('authorizationToken');

        try {
            $response = $this->httpClient->post(
                "{$baseUrl}/payments/v1/authorizations/{$authToken}/customer-token",
                [
                    'json'    => $request->getPayload(),
                    'headers' => $request->getHeaders(),
                ]
            );
        } catch (KlarnaCurlClientException $e) {
            return $this->wrapException($e);
        }

        return $this->wrapResponse($response, CustomerTokenResponse::class);
    }

    public function cancel(Request $request): Response
    {
        $baseUrl       = $request->getBaseUrl();
        $customerToken = $request->getQueryParameter('customerToken');

        try {
            $response = $this->httpClient->patch(
                "{$baseUrl}/customer-token/v1/tokens/{$customerToken}/status",
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
