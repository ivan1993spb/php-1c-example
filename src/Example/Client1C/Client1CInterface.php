<?php

namespace Example\Client1C;

use Example\Client1C\RequestTypes\RequestAddOrder;
use Example\Client1C\RequestTypes\RequestAddGoodToOrder;
use Example\Client1C\RequestTypes\RequestAddGoodsToOrder;
use Example\Client1C\RequestTypes\RequestConfirmOrder;
use Example\Client1C\RequestTypes\RequestResetOrder;

/**
 * Interface ClientInterface
 *
 * @package Example\Client1C
 */
interface Client1CInterface
{
    /**
     * addOrder adds new order
     *
     * @param \Example\Client1C\RequestTypes\RequestAddOrder $request
     * @return \Example\Client1C\ResponseTypes\ResponseAddOrder
     * @throws \Example\Client1C\Exceptions\MethodInvocationException
     */
    public function addOrder(RequestAddOrder $request);

    /**
     * addGoodToOrder adds a good to an order
     *
     * @param \Example\Client1C\RequestTypes\RequestAddGoodToOrder $request
     * @return \Example\Client1C\ResponseTypes\ResponseAddGoodToOrder
     * @throws \Example\Client1C\Exceptions\MethodInvocationException
     */
    public function addGoodToOrder(RequestAddGoodToOrder $request);

    /**
     * addGoodsToOrder adds goods to an order
     *
     * @param \Example\Client1C\RequestTypes\RequestAddGoodsToOrder $request
     * @return \Example\Client1C\ResponseTypes\ResponseAddGoodsToOrder
     * @throws \Example\Client1C\Exceptions\MethodInvocationException
     */
    public function addGoodsToOrder(RequestAddGoodsToOrder $request);

    /**
     * confirmOrder confirms an order
     *
     * @param \Example\Client1C\RequestTypes\RequestConfirmOrder $request
     * @return \Example\Client1C\ResponseTypes\ResponseConfirmOrder
     * @throws \Example\Client1C\Exceptions\MethodInvocationException
     */
    public function confirmOrder(RequestConfirmOrder $request);

    /**
     * resetOrder resets an order
     *
     * @param \Example\Client1C\RequestTypes\RequestResetOrder $request
     * @return \Example\Client1C\ResponseTypes\ResponseResetOrder
     * @throws \Example\Client1C\Exceptions\MethodInvocationException
     */
    public function resetOrder(RequestResetOrder $request);
}
