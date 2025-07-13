<?php
namespace App\Exceptions;

use Exception;

/**
 * Exception thrown when a user is not authenticated.
 *
 * Typically results in an HTTP 401 Unauthorized response,
 * indicating that authentication is required to access the resource.
 */
class UnauthorizedException extends Exception
{
    /**
     * Constructor.
     *
     * @param string $message  Exception message, defaults to 'Unauthorized'.
     * @param int $code       HTTP status code, defaults to 401.
     * @param Exception|null $previous Previous exception for chaining.
     */
    public function __construct(string $message = 'Unauthorized', int $code = 401, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
