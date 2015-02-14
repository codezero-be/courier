<?php namespace CodeZero\Courier;

interface Courier {

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
     */
    public function get($url, array $data = [], array $headers = [], $cacheMinutes = 0, HttpExceptionHandler $handler = null);

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
     */
    public function post($url, array $data = [], array $headers = [], $cacheMinutes = 0, HttpExceptionHandler $handler = null);

    /**
     * Send PUT request
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     * @param HttpExceptionHandler $handler
     *
     * @return Response
     */
    public function put($url, array $data = [], array $headers = [], HttpExceptionHandler $handler = null);

    /**
     * Send PATCH request
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     * @param HttpExceptionHandler $handler
     *
     * @return Response
     */
    public function patch($url, array $data = [], array $headers = [], HttpExceptionHandler $handler = null);

    /**
     * Send DELETE request
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     * @param HttpExceptionHandler $handler
     *
     * @return Response
     */
    public function delete($url, array $data = [], array $headers = [], HttpExceptionHandler $handler = null);

    /**
     * Set basic authentication
     *
     * @param string $username
     * @param string $password
     *
     * @return void
     */
    public function setBasicAuthentication($username, $password);

    /**
     * Unset basic authentication
     *
     * @return void
     */
    public function unsetBasicAuthentication();

    /**
     * Forget cached responses
     *
     * @return void
     */
    public function forgetCache();

}