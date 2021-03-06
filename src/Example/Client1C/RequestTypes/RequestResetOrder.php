<?php

namespace Example\Client1C\RequestTypes;

use Example\Client1C\Exceptions\InvalidRequestParamException;
use Example\Client1C\Request;
use Ramsey\Uuid\Uuid;

/**
 * Class RequestResetOrder
 *
 * @package Example\Client1C\RequestTypes
 */
class RequestResetOrder extends Request
{
    /**
     * @var string $orderUUID Order's UUID
     */
    public $orderUUID;

    /**
     * RequestResetOrder constructor
     *
     * @param string $orderUUID Order's UUID
     * @throws \Example\Client1C\Exceptions\InvalidRequestParamException
     */
    public function __construct($orderUUID)
    {
        if (empty($orderUUID)) {
            throw new InvalidRequestParamException("empty order UUID");
        }
        if (!Uuid::isValid($orderUUID)) {
            throw new InvalidRequestParamException("invalid order UUID");
        }

        $this->orderUUID = $orderUUID;
    }
}
