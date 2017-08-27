<?php

namespace Example\Client1C\Testing;

/**
 * Class DumbOrderItem
 *
 * @package Example\Client1C\Testing
 */
class DumbOrderItem
{
    /**
     * @var string $goodUUID
     * @var int    $count
     * @var float  $price
     */
    public $goodUUID, $count, $price;

    /**
     * DumbOrderItem constructor
     *
     * @param string $goodUUID
     * @param int    $count
     * @param float  $price
     */
    public function __construct($goodUUID, $count, $price)
    {
        $this->goodUUID = $goodUUID;
        $this->count = $count;
        $this->price = $price;
    }

    /**
     * getDiscountAmount calculates discount amount for order item
     *
     * @return float
     */
    public function getDiscountAmount()
    {
        // Для примера: скидка - 10%
        return $this->price * $this->count * 0.1;
    }
}
