<?php namespace CodeZero\Courier\Exceptions;

use CodeZero\Courier\Response;
use Exception;

class HttpException extends Exception {

    /**
     * Response
     *
     * @var Response
     */
    private $response;

    /**
     * Constructor
     *
     * @param string $message
     * @param int $code
     * @param Response $response
     * @param Exception $previous
     */
    public function __construct(Response $response = null, $message = '', $code = 0, Exception $previous = null)
    {
        $this->response = $response;

        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the response
     *
     * @return Response
     */
    public function response()
    {
        return $this->response;
    }

}