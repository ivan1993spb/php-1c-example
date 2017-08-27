<?php

namespace Example\Client1C\Testing\MemoryProviders;

/**
 * Class RandProviderGoodFactory
 *
 * @package Example\Client1C\Testing\MemoryProviders
 */
class RandProviderGoodFactory implements ProviderGoodFactory
{
    /**
     * @var int $maxPrice
     * @var int $maxCount
     */
    private $maxPrice = 0, $maxCount = 0;

    /**
     * RandProviderGoodFactory constructor
     *
     * @param int $maxPrice
     * @param int $maxCount
     */
    public function __construct($maxPrice = 0, $maxCount = 0)
    {
        $this->maxPrice = $maxPrice;
        $this->maxCount = $maxCount;
    }

    /**
     * @param string|null $goodUUID
     * @return ProviderGood
     */
    public function create($goodUUID = null): ProviderGood
    {
        $price = $this->maxPrice > 0 ? mt_rand(0, $this->maxPrice) : 0;
        $count = $this->maxCount > 0 ? mt_rand(0, $this->maxCount) : 0;

        return new ProviderGood($goodUUID, $price, $count);
    }
}
