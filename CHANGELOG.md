# Changelog

All Notable changes to Courier will be documented in this file.

## 2.1.0 (2015-02-16)

- Add `phpFastCache` as default `CacheManager` for non-Laravel projects

## 2.0.3 (2015-02-15)

- Fix bug in `getResponseType()` in the `CurlResponseParser` class

## 2.0.2 (2015-02-15)

- Improve response `toArray()` and `toObjects()` functionality

## 2.0.1 (2015-02-15)

- Add a `toObjects()` method to the `Response` class
- Add tests for the `Response` class

## 2.0.0 (2015-02-14)

- Rename `HttpRequestException` to `HttpException`.
- Add callback class parameter to `get()` etc. to handle any `HttpException`.
- Callback classes should implement `HttpExceptionHandler`.

## 1.1.2 (2015-02-05)

- Refactor

## 1.1.1 (2015-02-05)

- Update `LaravelCacheManager` and `CourierServiceProvider` for  Laravel 5.

## 1.1.0 (2015-02-03)

- Make `CurlCourier` constructor arguments optional for easier instantiation 
- Restructure for PSR-4 autoloading

## 1.0.4 (2014-08-27)

- Improve the request signature generator to uniquely identify and store requests in the cache.

## 1.0.3 (2014-08-27)

- Remove the `enableCache()`, `disableCache()` and `isCacheEnabled()` - instead specify the cache time as an argument to the `get()` and `post()` methods.

## 1.0.2 (2014-08-27)

- Update ServiceProvider for Laravel

## 1.0.1 (2014-08-26)

- Remove cache option from put, patch and delete requests. These should obviously not be cached.

## 1.0.0 (2014-08-25)

- Version 1.0.0 of Courier.