<?php

namespace Example\Client1C\Testing\Handlers;

use Example\Client1C\RequestTypes\RequestAddGoodToOrder;
use Example\Client1C\ResponseTypes\OrderItem;
use Example\Client1C\ResponseTypes\ResponseAddGoodToOrder;
use Example\Client1C\Testing\DumbOrder;
use Example\Client1C\Testing\Exceptions\GoodNotFoundException;
use Example\Client1C\Testing\Exceptions\GoodToRemoveNotFoundInOrderException;
use Example\Client1C\Testing\Exceptions\InvalidUUIDException;
use Example\Client1C\Testing\Exceptions\OrderConfirmedException;
use Example\Client1C\Testing\Exceptions\OrderNotFoundException;
use Example\Client1C\Testing\Exceptions\OrderResetedException;
use Example\Client1C\Testing\ProviderInterfaces\ProviderGoods;
use Example\Client1C\Testing\ProviderInterfaces\ProviderOrders;

/**
 * Class HandlerAddGoodToOrder
 *
 * @package Example\Client1C\Testing\Handlers
 */
class HandlerAddGoodToOrder
{
    const DELAY_MIN = 7;
    const DELAY_MAX = 11;

    const TIMEOUT = 11;

    /**
     * Эмитировать долгую обработку запроса, как в 1С, если true
     *
     * @var bool
     */
    private $useDelay = false;

    /**
     * @var \Example\Client1C\Testing\ProviderInterfaces\ProviderOrders $providerOrders
     * @var \Example\Client1C\Testing\ProviderInterfaces\ProviderGoods  $providerGoods
     */
    private $providerOrders, $providerGoods;

    /**
     * HandlerAddGoodToOrder constructor
     *
     * @param \Example\Client1C\Testing\ProviderInterfaces\ProviderOrders $providerOrders
     * @param \Example\Client1C\Testing\ProviderInterfaces\ProviderGoods  $providerGoods
     * @param bool                                                               $useDelay
     */
    public function __construct(ProviderOrders $providerOrders, ProviderGoods $providerGoods, $useDelay = false)
    {
        $this->providerOrders = $providerOrders;
        $this->providerGoods = $providerGoods;
        $this->useDelay = $useDelay;
    }

    /**
     * @param \Example\Client1C\RequestTypes\RequestAddGoodToOrder $request
     * @return \Example\Client1C\ResponseTypes\ResponseAddGoodToOrder
     */
    public function handle(RequestAddGoodToOrder $request)
    {
        // `status = 1` - `orderUUID` не переданн
        if (empty($request->orderUUID)) {
            return new ResponseAddGoodToOrder(ResponseAddGoodToOrder::STATUS_EMPTY_ORDER_UUID);
        }

        $order = null;
        try {
            $order = $this->providerOrders->getOrder($request->orderUUID);
        } catch (InvalidUUIDException $e) {
            // `status = 2` - `orderUUID` не корректный
            return new ResponseAddGoodToOrder(ResponseAddGoodToOrder::STATUS_INVALID_ORDER_UUID);
        } catch (OrderNotFoundException $e) {
            // `status = 3` - заказ c `orderUUID` не найден
            return new ResponseAddGoodToOrder(ResponseAddGoodToOrder::STATUS_ORDER_NOT_FOUND);
        }

        // `status = 4` - `goodUUID` не был передан
        if (empty($request->goodUUID)) {
            return new ResponseAddGoodToOrder(ResponseAddGoodToOrder::STATUS_EMPTY_GOOD_UUID, '', 0, $request->count,
                $this->prepareOrderItems($order), $order->getDiscountAmount());
        }

        $good = null;
        try {
            $good = $this->providerGoods->getGood($request->goodUUID);
        } catch (InvalidUUIDException $e) {
            // `status = 5` - `goodUUID` не корректный
            return new ResponseAddGoodToOrder(ResponseAddGoodToOrder::STATUS_INVALID_GOOD_UUID, $request->goodUUID, 0,
                $request->count, $this->prepareOrderItems($order), $order->getDiscountAmount());
        } catch (GoodNotFoundException $e) {
            // `status = 6` - товар с `goodUUID` не найден
            return new ResponseAddGoodToOrder(ResponseAddGoodToOrder::STATUS_GOOD_NOT_FOUND, $request->goodUUID, 0,
                $request->count, $this->prepareOrderItems($order), $order->getDiscountAmount());
        }

        // `status = 7` - `count` не был переданн
        if ($request->count === null || $request->count === '') {
            return new ResponseAddGoodToOrder(ResponseAddGoodToOrder::STATUS_EMPTY_COUNT, $request->goodUUID, 0,
                $request->count, $this->prepareOrderItems($order), $order->getDiscountAmount());
        }

        // `status = 8` - `count` не корректный (отрицательный)
        if ($request->count < 0) {
            return new ResponseAddGoodToOrder(ResponseAddGoodToOrder::STATUS_INVALID_COUNT, $request->goodUUID, 0,
                $request->count, $this->prepareOrderItems($order), $order->getDiscountAmount());
        }

        // Закончена проверка запроса, далее идет обработка

        if ($this->useDelay) {
            // Имитация долгого выполнения запроса, как в 1С
            $delay = mt_rand(self::DELAY_MIN, self::DELAY_MAX);
            sleep(min($delay, self::TIMEOUT));

            // `status = 16` - время ожидания старта выполнения задачи превышено
            if ($delay > self::TIMEOUT) {
                return new ResponseAddGoodToOrder(ResponseAddGoodToOrder::STATUS_TIME_IS_OUT, $request->goodUUID, 0,
                    $request->count, $this->prepareOrderItems($order), $order->getDiscountAmount());
            }
        }

        try {
            // `status = 13` - превышен лемит ожидания транзакции
            // Внутренняя ошибка 1С

            // `status = 14` - ошибка проведения заказа
            // Внутренняя ошибка 1С

            // `status = 15` - запрос на изменение устарел и не будет выполнен
            // Внутренняя логика вебсервиса - эту ошибку можно игнорировать при эмуляции

            // Сколько данного товара есть уже в заказе
            $count = $order->count($good->UUID());

            if ($count > 0) {
                // Разрезервируем товар, если есть в заказе
                $good->release($count);
            } elseif ($good->count() == 0) {
                // Проверим количество товара на складе, которое доступно для резерва
                // `status = 11` - товар не может быть добавлен из-за нулевого остатка
                return new ResponseAddGoodToOrder(ResponseAddGoodToOrder::STATUS_GOOD_HAVE_NOT, $good->UUID(), 0,
                    $request->count, $this->prepareOrderItems($order), $order->getDiscountAmount());
            }

            if ($request->count > 0) {
                // Резервируем заново столько, сколько нужно
                $reserved = $good->reserve($request->count);
                // Добавляем запись в заказ
                $order->add($good->UUID(), $reserved, $good->price());

                if ($reserved < $request->count) {
                    // `status = 10` - товар добавлен в корзину частично (`< count`)
                    return new ResponseAddGoodToOrder(ResponseAddGoodToOrder::STATUS_ADDED_NOT_ENOUGH, $good->UUID(), $reserved,
                        $request->count, $this->prepareOrderItems($order), $order->getDiscountAmount());
                }
            } else {
                $order->remove($good->UUID());
            }

            // `status = 0` - все хорошо
            return new ResponseAddGoodToOrder(ResponseAddGoodToOrder::STATUS_SUCCESS, $good->UUID(), $request->count,
                $request->count, $this->prepareOrderItems($order), $order->getDiscountAmount());

        } catch (OrderConfirmedException $e) {
            // `status = 9` - заказ уже подтвержден
            return new ResponseAddGoodToOrder(ResponseAddGoodToOrder::STATUS_ORDER_CONFIRMED, $good->UUID(), 0,
                $request->count, $this->prepareOrderItems($order), $order->getDiscountAmount());
        } catch (GoodToRemoveNotFoundInOrderException $e) {
            // `status = 12` - товар для удаления не найден в заказе
            return new ResponseAddGoodToOrder(ResponseAddGoodToOrder::STATUS_GOOD_NOT_FOUND_IN_ORDER, $good->UUID(), 0, 0,
                $this->prepareOrderItems($order), $order->getDiscountAmount());
        } catch (OrderResetedException $e) {
            // `status = 17` - заказ уже отменен
            return new ResponseAddGoodToOrder(ResponseAddGoodToOrder::STATUS_ORDER_RESETED, $good->UUID(), 0,
                $request->count, $this->prepareOrderItems($order), $order->getDiscountAmount());
        }
    }

    /**
     * @param DumbOrder $order
     *
     * @return array
     */
    private function prepareOrderItems(DumbOrder $order)
    {
        $items = [];

        foreach ($order->goods() as $good) {
            /** @var \Example\Client1C\Testing\DumbOrderItem $good */
            array_push($items, new OrderItem($good->goodUUID, $good->count, $good->price));
        }

        return $items;
    }
}
