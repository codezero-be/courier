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
        $array = null;
        $responseType = $this->getResponseType();
        $responseBody = $this->getBody();

        if ($responseType == 'application/binary')
        {
            $array = unserialize($responseBody);

            if ( ! is_array($array))
            {
                $array = null;
            }
        }
        elseif ($responseType == 'application/json')
        {
            $array = json_decode($responseBody, true);
        }

        if ( ! $array)
        {
            $msg = "Cannot convert the response content of type [$responseType] to an array";

            throw new ResponseConversionException($msg);
        }

        return $array;
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