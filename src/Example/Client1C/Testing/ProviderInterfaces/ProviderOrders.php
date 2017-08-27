<?php

namespace Example\Client1C\Testing\ProviderInterfaces;

use Example\Client1C\Testing\DumbOrder;

/**
 * Interface ProviderOrders
 *
 * @package Example\Client1C\Testing\ProviderInterfaces
 */
interface ProviderOrders
{
    /**
     * @param string $orderUUID
     * @return \Example\Client1C\Testing\DumbOrder
     * @throws \Example\Client1C\Testing\Exceptions\InvalidUUIDException
     * @throws \Example\Client1C\Testing\Exceptions\OrderNotFoundException
     */
    public function getOrder($orderUUID);

    /**
     * @param \Example\Client1C\Testing\DumbOrder
     * @throws \Example\Client1C\Testing\Exceptions\WritingOrderException
     */
    public function addOrder(DumbOrder $order);
}
