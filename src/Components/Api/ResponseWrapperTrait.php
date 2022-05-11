<?php

namespace BestitKlarnaOrderManagement\Components\Api;

use BestitKlarnaOrderManagement\Components\Api\Model\Error;
use BestitKlarnaOrderManagement\Components\Curl\Exception\RequestException;
use BestitKlarnaOrderManagement\Components\Curl\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\Serializer\SerializerInterface;
use BestitKlarnaOrderManagement\Components\Api\Response as ApiResponse;

/**
 * Creates a {@link Response} object from the given input.
 *
 * @package BestitKlarnaOrderManagement\Components\Api
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
trait ResponseWrapperTrait
{
    /** @var SerializerInterface */
    protected $serializer;

    /**
     * Since we can have 2 types of $guzzleResponse depending on the guzzle client version
     * we change the parameter typehint to mixed.
     *
     * @param Response|null  $guzzleResponse
     * @param string|null $modelClass
     *
     * @return ApiResponse
     */
    protected function wrapGuzzleResponse($guzzleResponse = null, $modelClass = null)
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

    /**
     * @param RequestException $e
     *
     * @return ApiResponse
     */
    protected function wrapGuzzleException(RequestException $e)
    {
        $guzzleResponse = $e->getResponse();

        if ($guzzleResponse === null) {
            $error = new Error();
            $error->errorCode = $e->getCode();
            $error->errorMessages[] = $e->getMessage();

            return ApiResponse::wrapError($error);
        }

        $rawResponse = (string) $guzzleResponse->getBody();

        /*
         * The 401 check is needed because in that case the body contains raw HTML.
         * So we cannot parse that response.
         */
        if (empty($rawResponse) || $guzzleResponse->getStatusCode() === SymfonyResponse::HTTP_UNAUTHORIZED) {
            $error = new Error();
            $error->errorCode = $guzzleResponse->getStatusCode();
            $error->errorMessages[] = $guzzleResponse->getReasonPhrase();
            $error->errorMessages[] = $e->getMessage();

            return ApiResponse::wrapError($error);
        }

        $response = ApiResponse::wrapError($this->serializer->deserialize($rawResponse, Error::class, 'json'));
        $response->setRawResponse($rawResponse);

        $response->setStatusCode($guzzleResponse->getStatusCode());

        return $response;
    }
}
