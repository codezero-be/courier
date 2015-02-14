<?php namespace CodeZero\Courier; 

use CodeZero\Courier\Exceptions\HttpException;

interface HttpExceptionHandler {

    /**
     * Handle HTTP errors
     *
     * @param HttpException $exception
     *
     * @return mixed
     */
    public function handleHttpException(HttpException $exception);

}