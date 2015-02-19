# Courier - HTTP Requests Made Easy

[![GitHub release](https://img.shields.io/github/release/codezero-be/courier.svg)]()
[![License](https://img.shields.io/packagist/l/codezero/courier.svg)]()
[![Build Status](https://img.shields.io/travis/codezero-be/courier.svg?branch=master)](https://travis-ci.org/codezero-be/courier)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/codezero-be/courier.svg)](https://scrutinizer-ci.com/g/codezero-be/courier)
[![Total Downloads](https://img.shields.io/packagist/dt/codezero/courier.svg)](https://packagist.org/packages/codezero/courier)

This package offers an easy to use set of functions to send HTTP requests in PHP.

## Features

- Easy to use GET, POST, PUT, PATCH and DELETE functions (see [usage](#usage))
- Send optional data and headers with your requests
- Use optional basic authentication with your requests
- Optional caching of GET and POST responses
- [Laravel 5](http://www.laravel.com/ "Laravel") ServiceProvider included!

## Installation

Install this package through Composer:

    "require": {
    	"codezero/courier": "2.*"
    }

## Implementation

### Manual

At this point there is only one Courier implementation: `CurlCourier`, which uses [codezero-be/curl](https://github.com/codezero-be/curl "codezero-be/curl") and the PHP cURL extension behind the scenes.

    use CodeZero\Courier\CurlCourier;

    $courier = new CurlCourier();

### Laravel 5

After installing, update your `config/app.php` file to include a reference to this package's service provider in the providers array:

    'providers' => [
	    'CodeZero\Courier\CourierServiceProvider'
    ]

## Inject Courier

Inject the Courier instance into your own class:

    use CodeZero\Courier\Courier; //=> The interface

    class MyClass {

	    private $courier;
	
	    public function __construct(Courier $courier)
	    {
	        $this->courier = $courier;
	    }
    }

Laravel will then find the `Courier` class automatically if you registered the service provider.
Or you can inject an instance manually: `$myClass = new MyClass($courier);`

## Send a Request

### Configure a Request:

	$url = 'http://my.site/api';
    $data = ['do' => 'something', 'with' => 'this']; //=> Optional
    $headers = ['Some Header' => 'Some Value']; //=> Optional

### Enable Caching

Optional number of minutes to cache the request/response. Until it expires Courier will not actually hit the requested URL, but return a cached response! If you use Laravel then Laravel's [`Cache`](http://laravel.com/docs/5.0/cache) will be used. If not then [`phpFastCache`](https://github.com/khoaofgod/phpfastcache) is used.

	$cacheMinutes = 30; //=> Default = 0 (no caching)

### Handle Exceptions

If the target server sends a HTTP error response >= 400, a `CodeZero\Courier\Exceptions\HttpException` will be thrown.

Your class can also implement the `CodeZero\Courier\HttpExceptionHandler` interface and add a [`handleHttpException`](#response-issues) method. If you pass Courier a reference to your class, then no exception will be thrown. Instead, the `HttpException` will be passed to your [`handleHttpException`](#response-issues) method, so you can throw your own exception or return any value back to the original caller.

	$handler = $this; //=> A reference to your class (optional)

### Send the Request: (one of the following)

	$response = $this->courier->get($url, $data, $headers, $cacheMinutes, $handler);
	$response = $this->courier->post($url, $data, $headers, $cacheMinutes, $handler);
	$response = $this->courier->put($url, $data, $headers, $handler);
	$response = $this->courier->patch($url, $data, $headers, $handler);
	$response = $this->courier->delete($url, $data, $headers, $handler);

All of these methods will return an instance of the `CodeZero\Courier\Response` class.

## Read the Response

### Get the Response Body

	$body = $response->getBody();

### Get Additional Request Info

	$httpCode = $response->getHttpCode(); //=> "200"
	$httpMessage = $response->getHttpMessage(); //=> "OK"
	$responseType = $response->getResponseType(); //=> "application/json"
	$responseCharset = $response->getResponseCharset(); //=> "UTF-8" 

### Convert the Response

You can convert a JSON or serialized response to an associative array or to an array of generic PHP objects. If you are sending API requests, chances are high that you get `application/json` data in return. The Flickr API `php_serial` responses are of type `application/binary` and serializable.

To convert this data just run:

	$array = $response->toArray();
    echo $array[0]['some']['nested']['key'];

	$objects = $response->toObjects();
    echo $objects[0]->some->nested->key;

If for some reason the conversion fails, a `CodeZero\Courier\Exceptions\ResponseConversionException` will be thrown.

## Caching

To enable caching, there are 2 conditions:

1. Courier needs to be instantiated with the `CodeZero\Courier\Cache\Cache` class and a valid implementation of the `CodeZero\Courier\Cache\CacheManager` has to be provided to this dependency. This is done automatically.
2. Caching minutes need to be greater than zero (default: 0)

### Cache Time

Each request can have a different cache time. Just specify it in the method call: 

	$this->courier->get($url, $data, $headers, $cacheMinutes); 

### Clear Cache at Runtime:

	$this->courier->forgetCache();

## Basic Authentication

If you require the use of basic authentication, you can enable this before sending the request:

	$this->courier->setBasicAuthentication('username', 'password');

You can also undo this:

	$this->courier->unsetBasicAuthentication();

## Exceptions

#### Request Issues

A `CodeZero\Courier\Exceptions\RequestException` will be thrown, if  the request could not be executed. This might be the case if your request is not properly configured (unsupported protocol, etc.).

#### Response Issues

A `CodeZero\Courier\Exceptions\HttpException` will be thrown, if there was a HTTP response error >= 400. Unless you are using the [`handleHttpException`](#handle-exceptions) callback. In that case you must add this method to your class:

    public function handleHttpException(HttpException $exception)
    {
        $errorMessage = $exception->getMessage();
        $errorCode = $exception->getCode();
        $response = $exception->response();
        $responseBody = $response->getBody();

        // If it's JSON:
        $array = $response->toArray();

        // throw your exception
        // or return something
    }

## Testing

    $ vendor/bin/phpspec run

## Security

If you discover any security related issues, please email <ivan@codezero.be> instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

---
[![Analytics](https://ga-beacon.appspot.com/UA-58876018-1/codezero-be/courier)](https://github.com/igrigorik/ga-beacon)