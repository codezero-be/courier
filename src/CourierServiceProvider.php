<?php namespace CodeZero\Courier;

use Illuminate\Support\ServiceProvider;

class CourierServiceProvider extends ServiceProvider {

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCacheManager();
        $this->registerCourier();
    }

    /**
     * Register the cache manager service binding
     */
    private function registerCacheManager()
    {
        $this->app->bind(
            'CodeZero\Courier\Cache\CacheManager',
            'CodeZero\Courier\Cache\LaravelCacheManager'
        );
    }

    /**
     * Register the courier service binding
     */
    private function registerCourier()
    {
        $this->app->bind('CodeZero\Courier\Courier', function($app)
        {
            $curlRequest = $app->make('CodeZero\Curl\Request');
            $responseParser = $app->make('CodeZero\Courier\CurlResponseParser');
            $cache = $app->make('CodeZero\Courier\Cache\Cache');

            return new CurlCourier($curlRequest, $responseParser, $cache);
        });
    }

}