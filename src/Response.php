<?php namespace CodeZero\Courier;

use CodeZero\Courier\Exceptions\ResponseConversionException;

class Response {

    /**
     * The Response Body
     *
     * @var string
     */
    private $responseBody;

    /**
     * Response Type
     *
     * @var string
     */
    private $responseType;

    /**
     * Response Charset
     *
     * @var string
     */
    private $responseCharset;

    /**
     * HTTP Response Code
     *
     * @var int
     */
    private $httpCode;

    /**
     * HTTP Response Message
     *
     * @var string
     */
    private $httpMessage;

    /**
     * Constructor
     *
     * @param string $responseBody
     * @param string $responseType
     * @param string $responseCharset
     * @param int $httpCode
     * @param string $httpMessage
     */
    public function __construct($responseBody, $responseType, $responseCharset, $httpCode, $httpMessage)
    {
        $this->responseBody = $responseBody;
        $this->responseType = $responseType;
        $this->responseCharset = $responseCharset;
        $this->httpCode = $httpCode;
        $this->httpMessage = $httpMessage;
    }

    /**
     * Get the response body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->responseBody;
    }

    /**
     * Get the response type
     *
     * @return string
     */
    public function getResponseType()
    {
        /**
         * Possible Response Types
         *
         * text/plain (Facebook access_token response)
         * application/json (Facebook, Twitter)
         * application/binary (Flickr - php_serial)
         * text/javascript (Flickr - json)
         */

        return $this->responseType;
    }

    /**
     * Get the response charset
     *
     * @return string
     */
    public function getResponseCharset()
    {
        return $this->responseCharset;
    }

    /**
     * Get the HTTP response code
     *
     * @return int
     */
    public function getHttpCode()
    {
        return $this->httpCode;
    }

    /**
     * Get the HTTP response message
     *
     * @return string
     */
    public function getHttpMessage()
    {
        return $this->httpMessage;
    }

    /**
     * Convert the response body to an array
     *
     * @return array
     * @throws ResponseConversionException
     */
    public function toArray()
    {
        $response = $this->convertJsonResponse(true) ?: $this->convertSerializedResponse();

        if ( ! is_array($response))
        {
            $this->throwResponseConversionException();
        }

        return $response;
    }

    /**
     * Convert the response body to PHP Objects
     *
     * @return array|object
     * @throws ResponseConversionException
     */
    public function toObjects()
    {
        $response = $this->convertJsonResponse();

        if ( ! $response)
        {
            $this->throwResponseConversionException();
        }

        return $response;
    }

    /**
     * Convert the JSON response to JSON or an array
     *
     * @param bool $toArray
     *
     * @return bool|mixed
     */
    private function convertJsonResponse($toArray = false)
    {
        if ($this->getResponseType() == 'application/json')
        {
            return json_decode($this->getBody(), $toArray);
        }

        return false;
    }

    /**
     * Convert the serialized response to an array
     *
     * @return bool|mixed
     */
    private function convertSerializedResponse()
    {
        if ($this->getResponseType() == 'application/binary')
        {
            $response = @unserialize($this->getBody());

            if ( ! is_array($response))
            {
                // Maybe the response was a serialized JSON string
                // So let's try to convert that to an array...
                return json_decode($response, true);
            }

            return $response;
        }

        return false;
    }

    /**
     * Throw a ResponseConversionException
     *
     * @throws ResponseConversionException
     */
    private function throwResponseConversionException()
    {
        $msg = "Could not convert the response content of type [{$this->getResponseType()}]";

        throw new ResponseConversionException($msg);
    }

    /**
     * Output the response body
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getBody();
    }

}