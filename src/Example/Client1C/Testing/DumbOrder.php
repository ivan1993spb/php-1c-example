<?php

namespace Example\Client1C\Testing;

use Example\Client1C\Testing\Exceptions\ConfirmEmptyOrderException;
use Example\Client1C\Testing\Exceptions\GoodToRemoveNotFoundInOrderException;
use Example\Client1C\Testing\Exceptions\OrderConfirmedException;
use Example\Client1C\Testing\Exceptions\OrderResetedException;
use Ramsey\Uuid\Uuid;

/**
 * Class DumbOrder
 *
 * @package Example\Client1C\Testing
 */
class DumbOrder
{
    /**
     * @var bool $confirmed
     * @var bool $reseted
     */
    private $confirmed = false, $reseted = false;

    /**
     * @var string $UUID
     * @var string $ID
     * @var int    $VAT
     * @var int    $payType
     * @var int    $deliveryType
     */
    private $UUID, $ID, $VAT = -1, $payType = -1, $deliveryType = -1;

    /**
     * @var \Example\Client1C\Testing\DumbOrderItem[]
     */
    private $goods = [];

    /**
     * ProviderOrder constructor
     *
     * @param int $VAT
     * @param int $payType
     * @param int $deliveryType
     */
    public function __construct($VAT = -1, $payType = -1, $deliveryType = -1)
    {
        $this->UUID = Uuid::getFactory()->uuid4()->toString();
        $this->ID = sprintf('TT00-%06d', rand(0, 1000));
        $this->VAT = $VAT;
        $this->payType = $payType;
        $this->deliveryType = $deliveryType;
    }

    /**
     * @return void
     * @throws \Example\Client1C\Testing\Exceptions\OrderConfirmedException
     * @throws \Example\Client1C\Testing\Exceptions\OrderResetedException
     */
    public function reset()
    {
        if ($this->confirmed) {
            throw new OrderConfirmedException();
        }

        if ($this->reseted) {
            throw new OrderResetedException();
        }

        $this->reseted = true;
    }

    /**
     * @return bool
     */
    public function reseted()
    {
        return $this->reseted;
    }

    /**
     * @return void
     * @throws \Example\Client1C\Testing\Exceptions\OrderConfirmedException
     * @throws \Example\Client1C\Testing\Exceptions\OrderResetedException
     * @throws \Example\Client1C\Testing\Exceptions\ConfirmEmptyOrderException
     */
    public function confirm()
    {
        if ($this->reseted) {
            throw new OrderResetedException();
        }

        if (empty($this->goods)) {
            throw new ConfirmEmptyOrderException();
        }

        if ($this->confirmed) {
            throw new OrderConfirmedException();
        }

        $this->confirmed = true;
    }

    /**
     * @return bool
     */
    public function confirmed()
    {
        return $this->confirmed;
    }

    /**
     * add добавит товар в заказ или изменит количество данного товара в заказе и вернет предыдущее количество
     *
     * @param string $goodUUID
     * @param int    $count
     * @param float  $price
     * @return int
     * @throws \Example\Client1C\Testing\Exceptions\OrderConfirmedException
     * @throws \Example\Client1C\Testing\Exceptions\OrderResetedException
     * @throws \Example\Client1C\Testing\Exceptions\GoodToRemoveNotFoundInOrderException
     */
    public function add($goodUUID, $count, $price)
    {
        if ($this->reseted) {
            throw new OrderResetedException();
        }

        if ($this->confirmed) {
            throw new OrderConfirmedException();
        }

        foreach ($this->goods as $index => &$good) {
            if ($good->goodUUID == $goodUUID) {
                $tmpCount = $good->count;

                if ($count > 0) {
                    $good->count = $count;
                    $good->price = $price;
                } else {
                    unset($this->goods[$index]);
                }

                return $tmpCount;
            }
        }

        if ($count > 0) {
            array_push($this->goods, new DumbOrderItem($goodUUID, $count, $price));
        } else {
            throw new GoodToRemoveNotFoundInOrderException();
        }

        return 0;
    }

    /**
     * @param string $goodUUID
     * @return int
     * @throws \Example\Client1C\Testing\Exceptions\OrderConfirmedException
     * @throws \Example\Client1C\Testing\Exceptions\OrderResetedException
     * @throws \Example\Client1C\Testing\Exceptions\GoodToRemoveNotFoundInOrderException
     */
    public function remove($goodUUID)
    {
        if ($this->reseted) {
            throw new OrderResetedException();
        }

        if ($this->confirmed) {
            throw new OrderConfirmedException();
        }

        foreach ($this->goods as $index => &$good) {
            if ($good->goodUUID == $goodUUID) {
                $tmpCount = $good->count;
                unset($this->goods[$index]);
                return $tmpCount;
            }
        }

        throw new GoodToRemoveNotFoundInOrderException();
    }

    /**
     * @param string $goodUUID
     *
     * @return int
     */
    public function count($goodUUID)
    {
        foreach ($this->goods as $good) {
            if ($good->goodUUID == $goodUUID) {
                return $good->count;
            }
        }

        return 0;
    }

    /**
     * @return \Generator
     */
    public function goods()
    {
        foreach ($this->goods as $good) {
            yield $good;
        }
    }

    /**
     * @return string
     */
    public function UUID()
    {
        return $this->UUID;
    }

    /**
     * @return string
     */
    public function ID()
    {
        return $this->ID;
    }

    /**
     * @return int
     */
    public function VAT()
    {
        return $this->VAT;
    }

    /**
     * @return int
     */
    public function payType()
    {
        return $this->payType;
    }

    /**
     * @return int
     */
    public function deliveryType()
    {
        return $this->deliveryType;
    }

    /**
     * getDiscountAmount calculates discount amount for order
     *
     * @return float
     */
    public function getDiscountAmount()
    {
        $discountAmount = 0;

        foreach ($this->goods as $good) {
            $discountAmount += $good->getDiscountAmount();
        }

        return $discountAmount;
    }
}
