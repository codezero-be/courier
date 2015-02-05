<?php namespace spec\CodeZero\Courier\Cache;

use Illuminate\Contracts\Cache\Repository as IlluminateCache;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class LaravelCacheManagerSpec extends ObjectBehavior {

    function let(IlluminateCache $cache)
    {
        $this->beConstructedWith($cache);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('CodeZero\Courier\Cache\LaravelCacheManager');
    }

    function it_stores_data_in_cache(IlluminateCache $cache)
    {
        $cacheKey = 'cacheKey';
        $cacheValue = 'value';
        $cacheMinutes = 30;
        $cacheIndex = 'courier_cached_requests';

        // Fetch index array from cache (constructor)
        $cache->rememberForever($cacheIndex, Argument::any())->shouldBeCalled()->willReturn([]);
        // Check uniaue index key
        $cache->has(Argument::type('string'))->shouldBeCalled()->willReturn(false);
        // Save index array in cache
        $cache->forever($cacheIndex, Argument::type('array'))->shouldBeCalled();
        // Save value in cache (with index key)
        $cache->put(Argument::type('string'), $cacheValue, $cacheMinutes)->shouldBeCalled();

        $this->store($cacheKey, $cacheValue, $cacheMinutes);
    }

    function it_returns_cached_data_if_a_valid_key_is_provided(IlluminateCache $cache)
    {
        $cacheKey = 'cacheKey';
        $cacheValue = 'value';
        $cacheMinutes = 30;
        $cacheIndex = 'courier_cached_requests';

        // Fetch index array from cache (constructor)
        $cache->rememberForever($cacheIndex, Argument::any())->willReturn([]);
        // Check uniaue index key
        $cache->has(Argument::type('string'))->willReturn(false);
        // Save index array in cache
        $cache->forever($cacheIndex, Argument::type('array'))->willReturn();
        // Save value in cache (with index key)
        $cache->put(Argument::type('string'), $cacheValue, $cacheMinutes)->willReturn();

        $this->store($cacheKey, $cacheValue, $cacheMinutes);

        // Check uniaue index key
        $cache->has(Argument::type('string'))->shouldBeCalled()->willReturn(true);
        // Get value from cache
        $cache->get(Argument::type('string'))->shouldBeCalled()->willReturn($cacheValue);

        $this->find($cacheKey)->shouldReturn($cacheValue);
    }

    function it_returns_false_if_an_invalid_key_is_provided(IlluminateCache $cache)
    {
        $cacheKey = 'cacheKey';
        $cacheIndex = 'courier_cached_requests';

        // Fetch index array from cache (constructor)
        $cache->rememberForever($cacheIndex, Argument::any())->willReturn([]);
        // Check uniaue index key
        $cache->has(Argument::type('string'))->shouldNotBeCalled();
        // Don't get value from cache
        $cache->get(Argument::type('string'))->shouldNotBeCalled();

        $this->find($cacheKey)->shouldReturn(false);
    }

    function it_returns_false_if_a_key_from_the_index_array_is_no_longer_in_cache(IlluminateCache $cache)
    {
        $cacheKey = 'cacheKey';
        $cacheValue = 'value';
        $cacheMinutes = 30;
        $cacheIndex = 'courier_cached_requests';

        // Fetch index array from cache (constructor)
        $cache->rememberForever($cacheIndex, Argument::any())->willReturn([]);
        // Check uniaue index key
        $cache->has(Argument::type('string'))->willReturn(false);
        // Save index array in cache
        $cache->forever($cacheIndex, Argument::type('array'))->willReturn();
        // Save value in cache (with index key)
        $cache->put(Argument::type('string'), $cacheValue, $cacheMinutes)->willReturn();

        $this->store($cacheKey, $cacheValue, $cacheMinutes);

        // Check uniaue index key
        $cache->has(Argument::type('string'))->shouldBeCalled()->willReturn(false);
        // Don't get value from cache
        $cache->get(Argument::type('string'))->shouldNotBeCalled();

        $this->find($cacheKey)->shouldReturn(false);
    }

    function it_forgets_all_cached_values(IlluminateCache $cache)
    {
        $cacheIndex = 'courier_cached_requests';
        $cachedValues = ['key1' => 'val1', 'key2' => 'val2', 'key3' => 'val3'];

        // Fetch index array from cache (constructor)
        $cache->rememberForever($cacheIndex, Argument::any())->willReturn($cachedValues);

        // Forget cached values with indexed keys
        foreach ($cachedValues as $key => $val)
        {
            $cache->has($key)->shouldBeCalled()->willReturn(true);
            $cache->forget($key)->shouldBeCalled();
        }

        // Save empty index array in cache
        $cache->forever($cacheIndex, [])->shouldBeCalled();

        $this->forget();
    }

}

namespace Illuminate\Cache;

class Repository {

    public function has($key) { }

    public function forever($key, $value) { }

    public function rememberForever($key, $default) { }

    public function put($key, $value, $minutes) { }

    public function get($key) { }

    public function forget($key) { }

}