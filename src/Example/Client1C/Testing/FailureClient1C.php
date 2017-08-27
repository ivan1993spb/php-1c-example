<?php

namespace Example\Client1C\Testing;

use Example\Client1C\Client1CInterface;
use Example\Client1C\Exceptions\MethodInvocationException;
use Example\Client1C\RequestTypes\RequestAddGoodsToOrder;
use Example\Client1C\RequestTypes\RequestAddGoodToOrder;
use Example\Client1C\RequestTypes\RequestAddOrder;
use Example\Client1C\RequestTypes\RequestConfirmOrder;
use Example\Client1C\RequestTypes\RequestResetOrder;

/**
 * Class FailureClient1C Mock-клиент для 1С для локальной разработки. FailureClient1C эмитирует ситуацию, когда сервер
 * 1С недоступен или упал
 *
 * @package Example\Client1C\Testing
 */
class FailureClient1C implements Client1CInterface
{
    /**
     * addOrder adds new order
     *
     * @param \Example\Client1C\RequestTypes\RequestAddOrder $request
     * @return \Example\Client1C\ResponseTypes\ResponseAddOrder
     * @throws \Example\Client1C\Exceptions\MethodInvocationException
     */
    public function addOrder(RequestAddOrder $request)
    {
        throw new MethodInvocationException('Cannot invoke method addOrder');
    }

    /**
     * addGoodToOrder adds a good to an order
     *
     * @param \Example\Client1C\RequestTypes\RequestAddGoodToOrder $request
     * @return \Example\Client1C\ResponseTypes\ResponseAddGoodToOrder
     * @throws \Example\Client1C\Exceptions\MethodInvocationException
     */
    public function addGoodToOrder(RequestAddGoodToOrder $request)
    {
        throw new MethodInvocationException('Cannot invoke method addGoodToOrder');
    }

    /**
     * addGoodsToOrder adds goods to an order
     *
     * @param \Example\Client1C\RequestTypes\RequestAddGoodsToOrder $request
     * @return \Example\Client1C\ResponseTypes\ResponseAddGoodsToOrder
     * @throws \Example\Client1C\Exceptions\MethodInvocationException
     */
    public function addGoodsToOrder(RequestAddGoodsToOrder $request)
    {
        throw new MethodInvocationException('Cannot invoke method addGoodsToOrder');
    }

    /**
     * confirmOrder confirms an order
     *
     * @param \Example\Client1C\RequestTypes\RequestConfirmOrder $request
     * @return \Example\Client1C\ResponseTypes\ResponseConfirmOrder
     * @throws \Example\Client1C\Exceptions\MethodInvocationException
     */
    public function confirmOrder(RequestConfirmOrder $request)
    {
        throw new MethodInvocationException('Cannot invoke method confirmOrder');
    }

    /**
     * resetOrder resets an order
     *
     * @param \Example\Client1C\RequestTypes\RequestResetOrder $request
     * @return \Example\Client1C\ResponseTypes\ResponseResetOrder
     * @throws \Example\Client1C\Exceptions\MethodInvocationException
     */
    public function resetOrder(RequestResetOrder $request)
    {
        throw new MethodInvocationException('Cannot invoke method resetOrder');
    }
}
