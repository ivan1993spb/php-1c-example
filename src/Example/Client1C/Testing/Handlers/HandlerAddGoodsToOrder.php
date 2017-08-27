<?php

namespace Example\Client1C\Testing\Handlers;

use Example\Client1C\RequestTypes\RequestAddGoodsToOrder;
use Example\Client1C\ResponseTypes\GoodState;
use Example\Client1C\ResponseTypes\OrderItem;
use Example\Client1C\ResponseTypes\ResponseAddGoodsToOrder;
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
 * Class HandlerAddGoodsToOrder
 *
 * @package Example\Client1C\Testing\Handlers
 */
class HandlerAddGoodsToOrder
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
     * HandlerAddGoodsToOrder constructor
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
     * @param \Example\Client1C\RequestTypes\RequestAddGoodsToOrder $request
     * @return \Example\Client1C\ResponseTypes\ResponseAddGoodsToOrder
     */
    public function handle(RequestAddGoodsToOrder $request)
    {
        // `status = 1` - `orderUUID` не передан
        if (empty($request->orderUUID)) {
            return new ResponseAddGoodsToOrder(ResponseAddGoodsToOrder::STATUS_EMPTY_ORDER_UUID);
        }

        $order = null;
        try {
            $order = $this->providerOrders->getOrder($request->orderUUID);
        } catch (InvalidUUIDException $e) {
            // `status = 2` - `orderUUID` не корректен
            return new ResponseAddGoodsToOrder(ResponseAddGoodsToOrder::STATUS_INVALID_ORDER_UUID);
        } catch (OrderNotFoundException $e) {
            // `status = 3` - заказ с UUID `orderUUID` не найден
            return new ResponseAddGoodsToOrder(ResponseAddGoodsToOrder::STATUS_ORDER_NOT_FOUND);
        }

        // Флаг успешности операции. $statusSuccess = true, если все добавления товара будут выполнены в полном объеме
        $statusSuccess = true;

        // `status = 4` - список товара для добавления `goods` не был передан
        // Не проверяем

        $goodStates = [];
        foreach ($request->getGoodItems() as $goodItem) {
            /** @var \Example\Client1C\RequestTypes\GoodItem $goodItem */

            // Поумолчанию ставим статус SUCCESS
            // `goods[n].status = 0` - все хорошо
            $status = GoodState::STATUS_SUCCESS;

            if (empty($goodItem->goodUUID)) {
                // `goods[n].status = 1` - `goods[n].goodUUID` не передан
                $status = GoodState::STATUS_EMPTY_GOOD_UUID;
                if ($statusSuccess) {
                    $statusSuccess = false;
                }
            } elseif ($goodItem->count === '' || $goodItem->count === null) {
                // `goods[n].status = 4` - `goods[n].count` не был передан
                $status = GoodState::STATUS_EMPTY_COUNT;
                if ($statusSuccess) {
                    $statusSuccess = false;
                }
            } elseif ($goodItem->count < 0) {
                // `goods[n].status = 5` - количество `goods[n].count` не корректно (отрицательное)
                $status = GoodState::STATUS_INVALID_COUNT;
                if ($statusSuccess) {
                    $statusSuccess = false;
                }
            }

            array_push($goodStates, new GoodState($status, $goodItem->goodUUID, 0, $goodItem->count));
        }

        // `status = 5` - список товара для добавления `goods` пустой
        if (empty($goodStates)) {
            return new ResponseAddGoodsToOrder(ResponseAddGoodsToOrder::STATUS_EMPTY_GOOD_LIST, $goodStates,
                $this->prepareOrderItems($order), $order->getDiscountAmount());
        }

        if ($this->useDelay) {
            // Имитация долгого выполнения запроса, как в 1С
            $delay = mt_rand(self::DELAY_MIN, self::DELAY_MAX);
            sleep(min($delay, self::TIMEOUT));

            // `status = 10` - максимальное время соединения превышено, при этом задача не начала выполняться
            if ($delay > self::TIMEOUT) {
                return new ResponseAddGoodsToOrder(ResponseAddGoodsToOrder::STATUS_TIME_IS_OUT, $goodStates,
                    $this->prepareOrderItems($order), $order->getDiscountAmount());
            }
        }

        foreach ($goodStates as &$goodState) {
            /** @var \Example\Client1C\ResponseTypes\GoodState $goodState */
            try {
                if ($goodState->status == GoodState::STATUS_SUCCESS) {
                    $good = $this->providerGoods->getGood($goodState->goodUUID);

                    // Сколько данного товара уже есть в заказе
                    $count = $order->count($good->UUID());

                    if ($count > 0) {
                        $good->release($count);
                    } elseif ($good->count() == 0) {
                        // Проверим количество товара на складе, которое доступно для резерва
                        // `goods[n].status = 7` - товар не может быть добавлен из-за нулевого остатка
                        if ($statusSuccess) {
                            $statusSuccess = false;
                        }
                        $goodState->status = GoodState::STATUS_GOOD_HAVE_NOT;
                        continue;
                    }

                    if ($goodState->required > 0) {
                        // Резервируем заново столько, сколько нужно
                        $goodState->count = $good->reserve($goodState->required);
                        // Добавляем запись в заказ
                        $order->add($good->UUID(), $goodState->count, $good->price());
                        if ($goodState->count < $goodState->required) {
                            // `goods[n].status = 6` - товар добавлен в корзину частично (меньше, чем требовалось `< goods[n].count`)
                            if ($statusSuccess) {
                                $statusSuccess = false;
                            }
                            $goodState->status = GoodState::STATUS_ADDED_NOT_ENOUGH;
                        }
                    } else {
                        $order->remove($good->UUID());
                    }
                }
            } catch (OrderConfirmedException $e) {
                // `status = 7` - заказ уже подтвержден
                return new ResponseAddGoodsToOrder(ResponseAddGoodsToOrder::STATUS_ORDER_CONFIRMED, $goodStates,
                    $this->prepareOrderItems($order), $order->getDiscountAmount());
            } catch (OrderResetedException $e) {
                // `status = 11` - заказ уже удален
                return new ResponseAddGoodsToOrder(ResponseAddGoodsToOrder::STATUS_ORDER_RESETED, $goodStates,
                    $this->prepareOrderItems($order), $order->getDiscountAmount());
            } catch (GoodToRemoveNotFoundInOrderException $e) {
                // `goods[n].status = 8` - товар для удаления не найден в заказе
                if ($statusSuccess) {
                    $statusSuccess = false;
                }
                $goodState->status = GoodState::STATUS_GOOD_TO_REMOVE_NOT_FOUND_IN_ORDER;
            } catch (InvalidUUIDException $e) {
                // `goods[n].status = 2` - `goods[n].goodUUID` не корректный
                if ($statusSuccess) {
                    $statusSuccess = false;
                }
                $goodState->status = GoodState::STATUS_INVALID_GOOD_UUID;
            } catch (GoodNotFoundException $e) {
                // `goods[n].status = 3` - товар с идентификатором `goods[n].goodUUID` не найден
                if ($statusSuccess) {
                    $statusSuccess = false;
                }
                $goodState->status = GoodState::STATUS_GOOD_NOT_FOUND;
            }

            // `goods[n].status = 9` - запрос на изменение устарел и не будет выполнен (по этой номенклатуре и заказу
            // поступил запрос с более поздней датой)
            // Внутренняя логика вебсервиса
        }

        // `status = 8` - превышен лемит ожидания транзакции
        // Внутренняя ошибка 1С

        // `status = 9` - ошибка проведения заказа
        // Внутренняя ошибка 1С

        if (!$statusSuccess) {
            // `status = 6` - запрос выполнен частично: добавлены не все запрошенные позиции в том количестве, которое
            // требовалось из-за того, что не хватило остатков
            return new ResponseAddGoodsToOrder(ResponseAddGoodsToOrder::STATUS_EXECUTED_NOT_ENOUGH, $goodStates,
                $this->prepareOrderItems($order), $order->getDiscountAmount());
        }

        // `status = 0` - все хорошо
        return new ResponseAddGoodsToOrder(ResponseAddGoodsToOrder::STATUS_SUCCESS, $goodStates, $this->prepareOrderItems($order),
            $order->getDiscountAmount());
    }

    /**
     * @param DumbOrder $order
     *
     * @return \Example\Client1C\Testing\DumbOrderItem[]
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
