<?php namespace CodeZero\Courier;

use Illuminate\Support\ServiceProvider;

class CourierServiceProvider extends ServiceProvider {

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('codezero/courier');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCourier();
        $this->registerCacheManager();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['courier'];
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
            $cacheEnabled = true;

            return new \CodeZero\Courier\CurlCourier($curlRequest, $responseParser, $cache, $cacheEnabled);
        });
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

}