<?php namespace CodeZero\Courier\Cache; 

use Illuminate\Contracts\Cache\Repository as IlluminateCache;

class LaravelCacheManager implements CacheManager {

    /**
     * Laravel Cache
     *
     * @var IlluminateCache
     */
    private $cache;

    /**
     * Cache Index
     *
     * @var array
     */
    private $cachedKeys;

    /**
     * Constructor
     *
     * @param IlluminateCache $cache
     */
    public function __construct(IlluminateCache $cache)
    {
        $this->cache = $cache;
        $this->cachedKeys = $this->getCachedKeys();
    }

    /**
     * Find in cache
     *
     * @param string $cacheKey
     *
     * @return mixed
     */
    public function find($cacheKey)
    {
        $key = array_search($cacheKey, $this->cachedKeys);

        if ($key != false and ! $this->cache->has($key))
        {
            unset($this->cachedKeys[$key]);
            $this->storeCachedKeys();

            return false;
        }

        return $key ? $this->cache->get($key) : false;
    }

    /**
     * Store in cache
     *
     * @param string $cacheKey
     * @param mixed $value
     * @param int $minutes
     *
     * @return void
     */
    public function store($cacheKey, $value, $minutes = 30)
    {
        $key = array_search($cacheKey, $this->cachedKeys);

        if ($key == false)
        {
            do
            {
                $key = uniqid();
            }
            while ($this->cache->has($key));

            $this->cachedKeys[$key] = $cacheKey;
            $this->storeCachedKeys();
        }

        $this->cache->put($key, $value, $minutes);
    }

    /**
     * Clear the cache
     *
     * @return void
     */
    public function forget()
    {
        foreach ($this->cachedKeys as $key => $signature)
        {
            if ($this->cache->has($key))
            {
                $this->cache->forget($key);
            }
        }

        $this->cachedKeys = [];
        $this->storeCachedKeys();
    }

    /**
     * Get the index from the cache and store a new one if it does not yet exist
     *
     * @return array
     */
    private function getCachedKeys()
    {
        return $this->cache->rememberForever('courier_cached_requests', function()
        {
            return [];
        });
    }

    /**
     * Store the index in the cache
     *
     * @return void
     */
    private function storeCachedKeys()
    {
        $this->cache->forever('courier_cached_requests', $this->cachedKeys);
    }

}