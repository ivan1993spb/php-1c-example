<?php

namespace Example\Client1C\Testing\ProviderInterfaces;

/**
 * Interface ProviderGoods
 *
 * @package Example\Client1C\Testing\ProviderInterfaces
 */
interface ProviderGoods
{
    /**
     * @param string $goodUUID
     * @return ProviderGood
     * @throws \Example\Client1C\Testing\Exceptions\InvalidUUIDException
     * @throws \Example\Client1C\Testing\Exceptions\GoodNotFoundException
     */
    public function getGood($goodUUID);
}
