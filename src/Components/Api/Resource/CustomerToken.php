<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Api\Resource;

use BestitKlarnaOrderManagement\Components\Api\Request;
use BestitKlarnaOrderManagement\Components\Api\Response;
use BestitKlarnaOrderManagement\Components\Api\ResponseWrapperTrait;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\Serializer\SerializerInterface;

class CustomerToken
{
    use ResponseWrapperTrait;

    /** @var HttpClient */
    protected $httpClient;

    public function __construct(HttpClient $client, SerializerInterface $serializer)
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
