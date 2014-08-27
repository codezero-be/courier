<?php namespace CodeZero\Courier\Cache;

use CodeZero\Courier\Response;

class Cache {

    /**
     * Cache Manager
     *
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * Request Signature Generator
     *
     * @var RequestSignatureGenerator
     */
    private $signature;

    /**
     * Constructor
     *
     * @param CacheManager $cacheManager
     * @param RequestSignatureGenerator $signature
     */
    public function __construct(CacheManager $cacheManager, RequestSignatureGenerator $signature)
    {
        $this->cacheManager = $cacheManager;
        $this->signature = $signature;
    }

    /**
     * Find a response in the cache
     *
     * @param string $method
     * @param string $url
     * @param array $data
     * @param array $headers
     * @param string $basicAuthCredentials
     *
     * @return Response|bool
     */
    public function findResponse($method, $url, array $data = [], array $headers = [], $basicAuthCredentials = '')
    {
        $signature = $this->signature->generate($method, $url, $data, $headers, $basicAuthCredentials);

        return $this->cacheManager->find($signature);
    }

    /**
     * Store a response in the cache
     *
     * @param Response $response
     * @param string $method
     * @param string $url
     * @param array $data
     * @param array $headers
     * @param string $basicAuthCredentials
     * @param int $minutes
     *
     * @return void
     */
    public function storeResponse(Response $response, $method, $url, array $data = [], array $headers = [], $basicAuthCredentials = '', $minutes = 30)
    {
        $signature = $this->signature->generate($method, $url, $data, $headers, $basicAuthCredentials);

        $this->cacheManager->store($signature, $response, $minutes);
    }

    /**
     * Clear the cache
     *
     * @return void
     */
    public function forget()
    {
        $this->cacheManager->forget();
    }

}