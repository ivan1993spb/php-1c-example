<?php

namespace Example\Client1C\Exceptions;

use Exception;
use Example\Client1C\Request;

/**
 * Class UnsupportedRequestException
 *
 * @package Example\Client1C\Exceptions
 */
class UnsupportedRequestException extends Exception
{
    /**
     * @var \Example\Client1C\Request
     */
    private $request;

    /**
     * UnsupportedRequestException constructor
     *
     * @param \Example\Client1C\Request $request
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(Request $request = null, $code = 0, Exception $previous = null)
    {
        $this->request = $request;

        parent::__construct('Sending of unsupported request', $code, $previous);
    }

    /**
     * @return \Example\Client1C\Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}
