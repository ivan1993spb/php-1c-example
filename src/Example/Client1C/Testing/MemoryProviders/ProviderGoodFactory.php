<?php

namespace Example\Client1C\Testing\MemoryProviders;

/**
 * Interface ProviderGoodFactory
 *
 * @package Example\Client1C\Testing\MemoryProviders
 */
interface ProviderGoodFactory
{
    /**
     * @param string|null $goodUUID
     * @return ProviderGood
     */
    public function create($goodUUID = null): ProviderGood;
}
