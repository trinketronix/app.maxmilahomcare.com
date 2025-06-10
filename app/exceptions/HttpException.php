<?php

namespace App\Exceptions;

use Exception;

abstract class HttpException extends Exception
{
    protected $statusCode = 500;

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}

class NotFoundException extends HttpException
{
    protected $statusCode = 404;

    public function __construct($message = "Resource not found", $code = 404, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

class ForbiddenException extends HttpException
{
    protected $statusCode = 403;

    public function __construct($message = "Access forbidden", $code = 403, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}