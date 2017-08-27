<?php

namespace Example\Client1C\RequestTypes;

use Example\Client1C\Exceptions\InvalidRequestParamException;
use Ramsey\Uuid\Uuid;

/**
 * Class GoodItem
 *
 * @package Example\Client1C\RequestTypes
 */
class GoodItem
{
    /**
     * @var string $goodUUID Goods UUID
     * @var int    $count
     */
    public $goodUUID, $count = -1;

    /**
     * GoodItem constructor
     *
     * @param string $goodUUID Goods UUID
     * @param int    $count
     * @throws \Example\Client1C\Exceptions\InvalidRequestParamException
     */
    public function __construct($goodUUID, $count)
    {
        if (empty($goodUUID)) {
            throw new InvalidRequestParamException("empty good UUID");
        }
        if (!Uuid::isValid($goodUUID)) {
            throw new InvalidRequestParamException("invalid good UUID");
        }
        if ($count < 0) {
            throw new InvalidRequestParamException("invalid count");
        }

        $this->goodUUID = $goodUUID;
        $this->count = $count;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return print_r($this, true);
    }
}
