<?php namespace CodeZero\Courier;

use CodeZero\Courier\Cache\Cache;
use CodeZero\Courier\Exceptions\HttpRequestException;
use CodeZero\Courier\Exceptions\RequestException;
use CodeZero\Curl\Request as CurlRequest;
use CodeZero\Curl\RequestException as CurlRequestException;

class CurlCourier implements Courier {

    /**
     * Curl Request
     *
     * @var CurlRequest
     */
    private $curl;

    /**
     * Curl Response Parser
     *
     * @var CurlResponseParser
     */
    private $responseParser;

    /**
     * Cache
     *
     * @var Cache
     */
    private $cache;

    /**
     * Basic Authentication Credentials
     *
     * @var string
     */
    private $basicAuthCredentials;

    /**
     * Constructor
     *
     * @param CurlRequest $curl
     * @param CurlResponseParser $responseParser
     * @param Cache $cache
     */
    public function __construct(CurlRequest $curl = null, CurlResponseParser $responseParser = null, Cache $cache = null)
    {
        $this->curl = $curl ?: new CurlRequest();
        $this->responseParser = $responseParser ?: new CurlResponseParser(new ResponseCodes());
        $this->cache = $cache;
        $this->basicAuthCredentials = '';
    }

    /**
     * Send GET request
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     * @param int $cacheMinutes
     *
     * @throws RequestException
     * @return Response
     */
    public function get($url, array $data = [], array $headers = [], $cacheMinutes = 0)
    {
        return $this->send('get', $url, $data, $headers, $cacheMinutes);
    }

    /**
     * Send POST request
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     * @param int $cacheMinutes
     *
     * @throws RequestException
     * @return Response
     */
    public function post($url, array $data = [], array $headers = [], $cacheMinutes = 0)
    {
        return $this->send('post', $url, $data, $headers, $cacheMinutes);
    }

    /**
     * Send PUT request
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     *
     * @throws RequestException
     * @return Response
     */
    public function put($url, array $data = [], array $headers = [])
    {
        return $this->send('put', $url, $data, $headers);
    }

    /**
     * Send PATCH request
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     *
     * @throws RequestException
     * @return Response
     */
    public function patch($url, array $data = [], array $headers = [])
    {
        return $this->send('patch', $url, $data, $headers);
    }

    /**
     * Send DELETE request
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     *
     * @throws RequestException
     * @return Response
     */
    public function delete($url, array $data = [], array $headers = [])
    {
        return $this->send('delete', $url, $data, $headers);
    }

    /**
     * Set basic authentication
     *
     * @param string $username
     * @param string $password
     *
     * @return void
     */
    public function setBasicAuthentication($username, $password)
    {
        $this->basicAuthCredentials = implode(':', [$username, $password]);
        $this->curl->setBasicAuthentication($username, $password);
    }

    /**
     * Unset basic authentication
     *
     * @return void
     */
    public function unsetBasicAuthentication()
    {
        $this->basicAuthCredentials = '';
        $this->curl->unsetBasicAuthentication();
    }

    /**
     * Forget cached responses
     *
     * @return void
     */
    public function forgetCache()
    {
        if ($this->cache)
        {
            $this->cache->forget();
        }
    }

    /**
     * Send request
     *
     * @param string $method
     * @param string $url
     * @param array $data
     * @param array $headers
     * @param int $cacheMinutes
     *
     * @throws RequestException
     * @return Response
     */
    private function send($method, $url, array $data, array $headers, $cacheMinutes = 0)
    {
        if ($response = $this->getCachedResponse($method, $url, $data, $headers))
        {
            return $response;
        }

        try
        {
            // Execute the appropriate method on the Curl request class
            $curlResponse = $this->curl->$method($url, $data, $headers);
            // Convert the response
            $response = $this->responseParser->parse($curlResponse);

            $this->throwExceptionOnErrors($response);
            $this->storeCachedResponse($response, $method, $url, $data, $headers, $cacheMinutes);

            return $response;
        }
        catch (CurlRequestException $exception)
        {
            $code = $exception->getCode();
            $message = $exception->getMessage();

            throw new RequestException($message, $code);
        }
    }

    /**
     * Get response from cache
     *
     * @param string $method
     * @param string $url
     * @param array $data
     * @param array $headers
     *
     * @return bool|Response
     */
    private function getCachedResponse($method, $url, array $data, array $headers)
    {
        if ($this->cache)
        {
            return $this->cache->findResponse($method, $url, $data, $headers, $this->basicAuthCredentials);
        }

        return false;
    }

    /**
     * Store response in cache
     *
     * @param Response $response
     * @param string $method
     * @param string $url
     * @param array $data
     * @param array $headers
     * @param int $minutes
     *
     * @return void
     */
    private function storeCachedResponse(Response $response, $method, $url, array $data, array $headers, $minutes)
    {
        if ($this->cache and $minutes > 0)
        {
            $this->cache->storeResponse($response, $method, $url, $data, $headers, $this->basicAuthCredentials, $minutes);
        }
    }

    /**
     * Check for any HTTP response errors
     *
     * @param Response $response
     *
     * @throws HttpRequestException
     * @return void
     */
    private function throwExceptionOnErrors(Response $response)
    {
        $httpCode = $response->getHttpCode();

        if ($httpCode >= 400)
        {
            // Get the description of the http error code
            $httpMessage = $response->getHttpMessage($httpCode);

            throw new HttpRequestException($response, $httpMessage, $httpCode);
        }
    }

}