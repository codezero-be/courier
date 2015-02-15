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
        if ($this->getResponseType() == 'application/json')
        {
            $response = $this->convertJsonToArray($this->getBody());
        }
        elseif ($this->getResponseType() == 'application/binary')
        {
            $response = $this->convertSerializedToArray($this->getBody());
        }
        else
        {
            $response = null;
        }

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
        if ($this->getResponseType() == 'application/json')
        {
            $response = $this->convertJsonToObjects($this->getBody());
        }
        elseif ($this->getResponseType() == 'application/binary')
        {
            $response = $this->convertSerializedToObjects($this->getBody());
        }
        else
        {
            $response = null;
        }

        if ( ! $response)
        {
            $this->throwResponseConversionException();
        }

        return $response;
    }

    /**
     * Convert a JSON response to an array
     *
     * @param string $data
     *
     * @return mixed|null
     */
    private function convertJsonToArray($data)
    {
        return $this->convertFromJson($data, true);
    }

    /**
     * Convert a JSON response to objects
     *
     * @param string $data
     *
     * @return mixed|null
     */
    private function convertJsonToObjects($data)
    {
        return $this->convertFromJson($data, false);
    }

    /**
     * Convert a serialized response to an array
     *
     * @param string $data
     *
     * @return array|null
     */
    private function convertSerializedToArray($data)
    {
        $data = @unserialize($data);

        // Maybe the response was a serialized JSON string
        // So let's try to convert that to an array...
        if (is_string($data))
        {
            return $this->convertJsonToArray($data);
        }

        // It could be an object or an array of object
        // but we want a associative array, so use
        // JSON to encode it and then decode it properly...
        if ((is_object($data) or is_array($data)) and $json = $this->convertToJson($data))
        {
            return $this->convertJsonToArray($json);
        }

        return null;
    }

    /**
     * Convert a serialized response to objects
     *
     * @param string $data
     *
     * @return array|object||null
     */
    private function convertSerializedToObjects($data)
    {
        $data = @unserialize($data);

        // If it is an object then
        // we can just return it...
        if (is_object($data))
        {
            return $data;
        }

        // It could be a associative array so use
        // JSON to encode it and then decode it properly...
        if (is_array($data) and $json = $this->convertToJson($data))
        {
            return $this->convertJsonToObjects($json);
        }

        return null;
    }

    /**
     * Convert a JSON string to objects or an associative array
     *
     * @param string $data
     * @param bool $toArray
     *
     * @return mixed|null
     */
    private function convertFromJson($data, $toArray = false)
    {
        return @json_decode($data, $toArray);
    }

    /**
     * Convert data to a JSON string
     *
     * @param mixed $data
     *
     * @return string|bool
     */
    private function convertToJson($data)
    {
        return @json_encode($data);
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