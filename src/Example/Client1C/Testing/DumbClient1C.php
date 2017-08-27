<?php

namespace Example\Client1C\Testing;

use Example\Client1C\Client1CInterface;
use Example\Client1C\Exceptions\MethodInvocationException;
use Example\Client1C\RequestTypes\RequestAddGoodsToOrder;
use Example\Client1C\RequestTypes\RequestAddGoodToOrder;
use Example\Client1C\RequestTypes\RequestAddOrder;
use Example\Client1C\RequestTypes\RequestConfirmOrder;
use Example\Client1C\RequestTypes\RequestResetOrder;
use Example\Client1C\Testing\Exceptions\HandlingRequestException;
use Example\Client1C\Testing\Handlers\HandlerAddGoodsToOrder;
use Example\Client1C\Testing\Handlers\HandlerAddGoodToOrder;
use Example\Client1C\Testing\Handlers\HandlerAddOrder;
use Example\Client1C\Testing\Handlers\HandlerConfirmOrder;
use Example\Client1C\Testing\Handlers\HandlerResetOrder;
use Example\Client1C\Testing\ProviderInterfaces\ProviderClients;
use Example\Client1C\Testing\ProviderInterfaces\ProviderGoods;
use Example\Client1C\Testing\ProviderInterfaces\ProviderOrders;

/**
 * Class DumbClient1C Mock-клиент для 1С для локальной разработки. Полностью эмитирует все действия 1С
 *
 * @package Example\Client1C
 */
class DumbClient1C implements Client1CInterface
{
    /**
     * @var \Example\Client1C\Testing\Handlers\HandlerAddOrder        $handlerAddOrder
     * @var \Example\Client1C\Testing\Handlers\HandlerResetOrder      $handlerResetOrder
     * @var \Example\Client1C\Testing\Handlers\HandlerConfirmOrder    $handlerConfirmOrder
     * @var \Example\Client1C\Testing\Handlers\HandlerAddGoodToOrder  $handlerAddGoodToOrder
     * @var \Example\Client1C\Testing\Handlers\HandlerAddGoodsToOrder $handlerAddGoodsToOrder
     */
    private $handlerAddOrder, $handlerResetOrder, $handlerConfirmOrder, $handlerAddGoodToOrder, $handlerAddGoodsToOrder;

    /**
     * DumbClient1C constructor
     *
     * @param ProviderGoods   $providerGoods
     * @param ProviderClients $providerClients
     * @param ProviderOrders  $providerOrders
     * @param bool            $useDelay
     */
    public function __construct(ProviderGoods $providerGoods, ProviderClients $providerClients, ProviderOrders $providerOrders,
        $useDelay = false)
    {
        $this->handlerAddOrder = new HandlerAddOrder($providerClients, $providerOrders, $useDelay);
        $this->handlerResetOrder = new HandlerResetOrder($providerOrders, $providerGoods, $useDelay);
        $this->handlerConfirmOrder = new HandlerConfirmOrder($providerOrders, $useDelay);
        $this->handlerAddGoodToOrder = new HandlerAddGoodToOrder($providerOrders, $providerGoods, $useDelay);
        $this->handlerAddGoodsToOrder = new HandlerAddGoodsToOrder($providerOrders, $providerGoods, $useDelay);
    }

    /**
     * addOrder adds new order
     *
     * @param \Example\Client1C\RequestTypes\RequestAddOrder $request
     * @return \Example\Client1C\ResponseTypes\ResponseAddOrder
     * @throws \Example\Client1C\Exceptions\MethodInvocationException
     */
    public function addOrder(RequestAddOrder $request)
    {
        try {
            return $this->handlerAddOrder->handle($request)->attachTimeNow()->attachRequest($request);
        } catch (HandlingRequestException $e) {
            throw new MethodInvocationException('Method AddOrder', 0, $e);
        }
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
        try {
            return $this->handlerAddGoodToOrder->handle($request)->attachTimeNow()->attachRequest($request);
        } catch (HandlingRequestException $e) {
            throw new MethodInvocationException('Method AddGoodToOrder', 0, $e);
        }
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
        try {
            return $this->handlerAddGoodsToOrder->handle($request)->attachTimeNow()->attachRequest($request);
        } catch (HandlingRequestException $e) {
            throw new MethodInvocationException('Method AddGoodsToOrder', 0, $e);
        }
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
        try {
            return $this->handlerConfirmOrder->handle($request)->attachTimeNow()->attachRequest($request);
        } catch (HandlingRequestException $e) {
            throw new MethodInvocationException('Method ConfirmOrder', 0, $e);
        }
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
        try {
            return $this->handlerResetOrder->handle($request)->attachTimeNow()->attachRequest($request);
        } catch (HandlingRequestException $e) {
            throw new MethodInvocationException('Method ResetOrder', 0, $e);
        }
    }
}
