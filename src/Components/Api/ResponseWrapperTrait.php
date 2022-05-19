<?php

namespace BestitKlarnaOrderManagement\Components\Api;

use BestitKlarnaOrderManagement\Components\Api\Model\Error;
use BestitKlarnaOrderManagement\Components\Api\Response as ApiResponse;
use BestitKlarnaOrderManagement\Components\Curl\Exception\KlarnaCurlClientException;
use BestitKlarnaOrderManagement\Components\Curl\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\Serializer\SerializerInterface;

trait ResponseWrapperTrait
{
    /** @var SerializerInterface */
    protected $serializer;

    /**
     * Since we can have 2 types of $guzzleResponse depending on the guzzle client version
     * we change the parameter typehint to mixed.
     *
     * @param null|Response $guzzleResponse
     * @param null|string   $modelClass
     */
    protected function wrapResponse($guzzleResponse = null, $modelClass = null): ApiResponse
    {
        if ($guzzleResponse === null) {
            return ApiResponse::wrapEmptySuccessResponse();
        }

        $rawResponse = (string) $guzzleResponse->getBody();

        if ($modelClass !== null && !empty($rawResponse)) {
            $response = ApiResponse::wrapObject($this->serializer->deserialize($rawResponse, $modelClass, 'json'));
        } else {
            $response = ApiResponse::wrapEmptySuccessResponse();
        }

        $response->setRawResponse($rawResponse);

        $response->setStatusCode($guzzleResponse->getStatusCode());

        return $response;
    }

    protected function wrapException(KlarnaCurlClientException $e): ApiResponse
    {
        $response = $e->getResponse();
        $error    = $response->getError();

        if ($error !== null) {
            $response = ApiResponse::wrapError($error);
            $response->setRawResponse($response->getRawResponse());
            $response->setStatusCode($response->getStatusCode());

            return $response;
        }

        $error       = new Error();
        $rawResponse = (string) $response->getBody();

        /*
         * The 401 check is needed because in that case the body contains raw HTML.
         * So we cannot parse that response.
         */
        if (empty($rawResponse) || $e->getCode() === SymfonyResponse::HTTP_UNAUTHORIZED) {
            $error->errorCode       = $e->getCode();
            $error->errorMessages[] = $e->getMessage();
        }

        return ApiResponse::wrapError($error);
    }
}
