<?php

namespace Example\Client1C\Testing\MemoryProviders;

/**
 * Class ConstProviderGoodFactory
 *
 * @package Example\Client1C\Testing\MemoryProviders
 */
class ConstProviderGoodFactory implements ProviderGoodFactory
{
    /**
     * @var int $price
     * @var int $count
     */
    private $price = 0, $count = 0;

    /**
     * ConstProviderGoodFactory constructor
     *
     * @param int $price
     * @param int $count
     */
    public function __construct($price = 0, $count = 0)
    {
        $this->price = $price;
        $this->count = $count;
    }

    /**
     * @param string|null $goodUUID
     * @return ProviderGood
     */
    public function create($goodUUID = null): ProviderGood
    {
        return new ProviderGood($goodUUID, $this->price, $this->count);
    }
}
