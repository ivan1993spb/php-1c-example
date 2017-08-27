<?php

namespace Example\Client1C\ResponseTypes;

/**
 * Class OrderItem
 *
 * @package Example\Client1C\ResponseTypes
 */
class OrderItem
{
    /**
     * @var string $goodUUID Good's UUID
     * @var int    $count    Count items for order
     * @var float  $price    Price of order item
     */
    public $goodUUID, $count = -1, $price = -1;

    /**
     * OrderItem constructor.
     *
     * @param string|null $goodUUID
     * @param int         $count
     * @param int         $price
     */
    public function __construct($goodUUID = null, $count = 0, $price = 0)
    {
        $this->goodUUID = $goodUUID;
        $this->count = $count;
        $this->price = $price;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return print_r($this, true);
    }
}
