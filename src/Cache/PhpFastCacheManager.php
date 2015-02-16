<?php namespace CodeZero\Courier\Cache;

use phpFastCache;

if ( ! function_exists('phpFastCache'))
{
    require_once __DIR__ . '/../../vendor/phpfastcache/phpfastcache/phpfastcache.php';
}

class PhpFastCacheManager implements CacheManager {

    /**
     * PhpFastCache
     *
     * @var phpFastCache
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
     */
    public function __construct()
    {
        $this->setCachePath();
        $this->cache = phpFastCache();
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

        if ($key and ! $this->cache->isExisting($key))
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
            while ($this->cache->isExisting($key));

            $this->cachedKeys[$key] = $cacheKey;
            $this->storeCachedKeys();
        }

        $this->cache->set($key, $value, $minutes * 60); //=> From seconds
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
            if ($this->cache->isExisting($key))
            {
                $this->cache->delete($key);
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
        $key = 'courier_cached_requests';

        if ($this->cache->isExisting($key))
        {
            return $this->cache->get($key);
        }

        $newIndex = [];
        $this->cache->set($key, $newIndex);

        return $newIndex;
    }

    /**
     * Store the index in the cache
     *
     * @return void
     */
    private function storeCachedKeys()
    {
        $this->cache->set('courier_cached_requests', $this->cachedKeys);
    }

    /**
     * Set the path where cache files should be stored
     *
     * @return void
     */
    private function setCachePath()
    {
        $cachePath = __DIR__ . '/../../cache';

        if ( ! is_dir($cachePath))
        {
            mkdir($cachePath);
            chmod($cachePath, 0777);
        }

        phpFastCache::setup("path", $cachePath);
    }

}