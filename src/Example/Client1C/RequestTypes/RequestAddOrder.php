<?php

namespace Example\Client1C\RequestTypes;

use Example\Client1C\Exceptions\InvalidRequestParamException;
use Example\Client1C\Request;
use Ramsey\Uuid\Uuid;

/**
 * Class RequestAddOrder
 *
 * @package Example\Client1C\RequestTypes
 */
class RequestAddOrder extends Request
{
    /**
     * @var string $clientUUID Client's UUID
     */
    public $clientUUID;

    /**
     * RequestAddOrder constructor
     *
     * @param string $clientUUID Client's UUID
     * @throws \Example\Client1C\Exceptions\InvalidRequestParamException
     */
    public function __construct($clientUUID)
    {
        if (empty($clientUUID)) {
            throw new InvalidRequestParamException("empty client UUID");
        }
        if (!Uuid::isValid($clientUUID)) {
            throw new InvalidRequestParamException("invalid client UUID");
        }

        $this->clientUUID = $clientUUID;
    }
}
