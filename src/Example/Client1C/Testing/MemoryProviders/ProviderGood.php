<?php

namespace Example\Client1C\Testing\MemoryProviders;

use Example\Client1C\Testing\ProviderInterfaces\ProviderGood as ProviderGoodInterface;
use Ramsey\Uuid\Uuid;

/**
 * Class ProviderGood
 *
 * @package Example\Client1C\Testing\MemoryProviders
 */
class ProviderGood implements ProviderGoodInterface
{
    /**
     * @var string $UUID
     * @var float  $price
     * @var int    $count
     */
    private $UUID, $price, $count;

    /**
     * ProviderGood constructor
     *
     * @param string|null $goodUUID
     * @param float       $price
     * @param int         $count
     */
    public function __construct($goodUUID = null, $price = 0.0, $count = 0)
    {
        if ($count < 0) {
            $count = 0;
        }
        if ($price < 0) {
            $price = 0;
        }
        if ($goodUUID == null) {
            $goodUUID = Uuid::getFactory()->uuid4()->toString();
        }
        $this->UUID = $goodUUID;
        $this->price = $price;
        $this->count = $count;
    }

    /**
     * @return string
     */
    public function UUID()
    {
        return $this->UUID;
    }

    /**
     * @return float
     */
    public function price()
    {
        return $this->price;
    }

    /**
     * Количество незарезервированного товара на складе
     *
     * @return int
     */
    public function count()
    {
        return $this->count;
    }

    /**
     * Пытается зарезервировать переданное количество товара и возвращает число - сколько удалось зарезервировать
     *
     * @param int $count
     * @return int
     */
    public function reserve($count)
    {
        if ($this->count >= $count) {
            $this->count -= $count;
            return $count;
        }

        $count = $this->count;
        $this->count = 0;
        return $count;
    }

    /**
     * Разрезервирует переданное количество товара и вернет общее количество
     *
     * @param int $count
     * @return int
     */
    public function release($count)
    {
        $this->count += $count;
        return $this->count;
    }
}
