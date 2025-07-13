<?php
namespace App\Exceptions;

use Exception;

/**
 * Exception thrown when a user is authenticated but lacks
 * the necessary permissions to access a resource or perform an action.
 *
 * Typically results in an HTTP 403 Forbidden response.
 */
class ForbiddenException extends Exception
{
    /**
     * Constructor.
     *
     * @param string $message  Exception message, defaults to 'Forbidden'.
     * @param int $code       HTTP status code, defaults to 403.
     * @param Exception|null $previous Previous exception for chaining.
     */
    public function __construct(string $message = 'Forbidden', int $code = 403, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
