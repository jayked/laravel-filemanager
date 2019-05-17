<?php

namespace Jayked\Laravelfilemanager\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ForbiddenDirectoryException extends HttpException
{
    /**
     * @param string     $message  The internal exception message
     * @param \Exception $previous The previous exception
     * @param int        $code     The internal exception code
     * @param array      $headers
     */
    public function __construct(string $message = null, \Exception $previous = null, ?int $code = 0, array $headers = [])
    {
        parent::__construct(403, $message, $previous, $headers, $code);
    }
}