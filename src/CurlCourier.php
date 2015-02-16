<?php namespace CodeZero\Courier;

use CodeZero\Courier\Cache\Cache;
use CodeZero\Courier\Exceptions\HttpException;
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
        $this->responseParser = $responseParser ?: new CurlResponseParser();
        $this->cache = $cache ?: new Cache();
        $this->basicAuthCredentials = '';
    }

    /**
     * Send GET request
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     * @param int $cacheMinutes
     * @param HttpExceptionHandler $handler
     *
     * @return Response
     * @throws HttpException
     * @throws RequestException
     * @throws \Exception
     */
    public function get($url, array $data = [], array $headers = [], $cacheMinutes = 0, HttpExceptionHandler $handler = null)
    {
        return $this->send('get', $url, $data, $headers, $cacheMinutes, $handler);
    }

    /**
     * Send POST request
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     * @param int $cacheMinutes
     * @param HttpExceptionHandler $handler
     *
     * @return Response
     * @throws HttpException
     * @throws RequestException
     * @throws \Exception
     */
    public function post($url, array $data = [], array $headers = [], $cacheMinutes = 0, HttpExceptionHandler $handler = null)
    {
        return $this->send('post', $url, $data, $headers, $cacheMinutes, $handler);
    }

    /**
     * Send PUT request
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     * @param HttpExceptionHandler $handler
     *
     * @return Response
     * @throws HttpException
     * @throws RequestException
     * @throws \Exception
     */
    public function put($url, array $data = [], array $headers = [], HttpExceptionHandler $handler = null)
    {
        return $this->send('put', $url, $data, $headers, $handler);
    }

    /**
     * Send PATCH request
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     * @param HttpExceptionHandler $handler
     *
     * @return Response
     * @throws HttpException
     * @throws RequestException
     * @throws \Exception
     */
    public function patch($url, array $data = [], array $headers = [], HttpExceptionHandler $handler = null)
    {
        return $this->send('patch', $url, $data, $headers, $handler);
    }

    /**
     * Send DELETE request
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     * @param HttpExceptionHandler $handler
     *
     * @return Response
     * @throws HttpException
     * @throws RequestException
     * @throws \Exception
     */
    public function delete($url, array $data = [], array $headers = [], HttpExceptionHandler $handler = null)
    {
        return $this->send('delete', $url, $data, $headers, $handler);
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
     * @param HttpExceptionHandler $handler
     *
     * @return Response
     * @throws HttpException
     * @throws RequestException
     */
    private function send($method, $url, array $data, array $headers, $cacheMinutes = 0, HttpExceptionHandler $handler = null)
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

            $this->throwExceptionOnHttpErrors($response);
            $this->storeCachedResponse($response, $method, $url, $data, $headers, $cacheMinutes);

            return $response;
        }
        catch (CurlRequestException $exception)
        {
            $code = $exception->getCode();
            $message = $exception->getMessage();

            throw new RequestException($message, $code, $exception);
        }
        catch (HttpException $exception)
        {
            if ( ! $handler)
            {
                throw $exception;
            }

            return $handler->handleHttpException($exception);
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
     * @return void
     * @throws HttpException
     */
    private function throwExceptionOnHttpErrors(Response $response)
    {
        $httpCode = $response->getHttpCode();

        if ($httpCode >= 400)
        {
            // Get the description of the http error code
            $httpMessage = $response->getHttpMessage($httpCode);

            throw new HttpException($response, $httpMessage, $httpCode);
        }
    }

}