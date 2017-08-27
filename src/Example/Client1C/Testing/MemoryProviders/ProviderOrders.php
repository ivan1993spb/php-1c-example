<?php

namespace Example\Client1C\Testing\MemoryProviders;

use Example\Client1C\Testing\DumbOrder;
use Example\Client1C\Testing\Exceptions\InvalidUUIDException;
use Example\Client1C\Testing\Exceptions\OrderNotFoundException;
use Example\Client1C\Testing\ProviderInterfaces\ProviderOrders as ProviderOrdersInterface;
use Ramsey\Uuid\Uuid;

/**
 * Class ProviderOrders
 *
 * @package Example\Client1C\Testing\MemoryProviders
 */
class ProviderOrders implements ProviderOrdersInterface
{
    /**
     * @var \Example\Client1C\Testing\DumbOrder[]
     */
    private $orders = [];

    /**
     * @param string $orderUUID
     * @return \Example\Client1C\Testing\DumbOrder
     * @throws \Example\Client1C\Testing\Exceptions\InvalidUUIDException
     * @throws \Example\Client1C\Testing\Exceptions\OrderNotFoundException
     */
    public function getOrder($orderUUID)
    {
        if (!Uuid::isValid($orderUUID)) {
            throw new InvalidUUIDException();
        }

        foreach ($this->orders as $order) {
            if ($order->UUID() === $orderUUID) {
                return $order;
            }
        }

        throw new OrderNotFoundException();
    }

    /**
     * @param \Example\Client1C\Testing\DumbOrder $order
     * @throws \Example\Client1C\Testing\Exceptions\WritingOrderException
     */
    public function addOrder(DumbOrder $order)
    {
        array_push($this->orders, $order);
    }
}
