<?php namespace CodeZero\Courier;

use CodeZero\Curl\Response as CurlResponse;

class CurlResponseParser {

    /**
     * Response Messages
     *
     * @var ResponseCodes
     */
    private $responseCodes;

    /**
     * Constructor
     *
     * @param ResponseCodes $responseCodes
     */
    public function __construct(ResponseCodes $responseCodes)
    {
        $this->responseCodes = $responseCodes ?: new ResponseCodes();
    }

    /**
     * Convert the Curl response into a Courier response
     *
     * @param CurlResponse $curlResponse
     *
     * @return Response
     */
    public function parse(CurlResponse $curlResponse)
    {
        $responseBody = $curlResponse->getBody();

        $contentType = $curlResponse->info()->getContentType();
        $responseType = $this->getResponseType($contentType);
        $responseCharset = $this->getResponseCharset($contentType);

        $httpCode = $curlResponse->info()->getHttpCode();
        $httpMessage = $this->responseCodes->getMessage($httpCode);

        return new Response($responseBody, $responseType, $responseCharset, $httpCode, $httpMessage);
    }

    /**
     * Get the response type
     *
     * @param string $contentType
     *
     * @return string
     */
    private function getResponseType($contentType)
    {
        $length = strpos($contentType, ';') ?: null;
        $type = $length
            ? substr($contentType, 0, $length)
            : $contentType;

        return strtolower(trim($type));
    }

    /**
     * Get the response charset
     *
     * @param string $contentType
     *
     * @return string
     */
    private function getResponseCharset($contentType)
    {
        $start = strpos($contentType, '=');
        $type = '';

        if ($start !== false)
        {
            $type = strtoupper(trim(substr($contentType, $start + 1)));
        }

        return $type;
    }

}