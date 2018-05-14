<?php

namespace BestitKlarnaOrderManagement\Components\Api\Resource;

use BestitKlarnaOrderManagement\Components\Api\Request;
use BestitKlarnaOrderManagement\Components\Api\Response;
use BestitKlarnaOrderManagement\Components\Api\ResponseWrapperTrait;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Interface to interact with Klarna refund(s).
 *
 * @package BestitKlarnaOrderManagement\Components\Api\Resource
 *
 * @author Senan Sharhan <senan.sharhan@bestit-online.de>
 */
class Refund
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
    public function create(Request $request)
    {
        $baseUrl = $request->getBaseUrl();
        $orderId = $request->getQueryParameter('order_id');

        try {
            $response = $this->httpClient->post("{$baseUrl}/ordermanagement/v1/orders/{$orderId}/refunds", [
                'json' => $request->getPayload(),
                'headers' => $request->getHeaders()
            ]);
        } catch (RequestException $e) {
            return $this->wrapGuzzleException($e);
        }

        return $this->wrapGuzzleResponse($response);
    }
}
