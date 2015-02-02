<?php namespace CodeZero\Courier\Cache;

interface CacheManager {

    /**
     * Find in cache
     *
     * @param string $cacheKey
     *
     * @return mixed
     */
    public function find($cacheKey);

    /**
     * Store in cache
     *
     * @param string $cacheKey
     * @param mixed $value
     * @param int $minutes
     *
     * @return void
     */
    public function store($cacheKey, $value, $minutes = 30);

    /**
     * Clear the cache
     *
     * @return void
     */
    public function forget();

}