<?php

namespace Example\Client1C\Testing\Handlers;

use Example\Client1C\RequestTypes\RequestResetOrder;
use Example\Client1C\ResponseTypes\ResponseResetOrder;
use Example\Client1C\Testing\DumbOrderItem;
use Example\Client1C\Testing\Exceptions\GoodNotFoundException;
use Example\Client1C\Testing\Exceptions\GoodToRemoveNotFoundInOrderException;
use Example\Client1C\Testing\Exceptions\InvalidUUIDException;
use Example\Client1C\Testing\Exceptions\OrderConfirmedException;
use Example\Client1C\Testing\Exceptions\OrderNotFoundException;
use Example\Client1C\Testing\Exceptions\OrderResetedException;
use Example\Client1C\Testing\ProviderInterfaces\ProviderGoods;
use Example\Client1C\Testing\ProviderInterfaces\ProviderOrders;

/**
 * Class HandlerAddOrder
 *
 * @package Example\Client1C\Testing\Handlers
 */
class HandlerResetOrder
{
    const DELAY_MIN = 7;
    const DELAY_MAX = 11;

    const TIMEOUT = 11;

    /**
     * @var \Example\Client1C\Testing\ProviderInterfaces\ProviderOrders $providerOrders
     * @var \Example\Client1C\Testing\ProviderInterfaces\ProviderGoods  $providerGoods
     */
    private $providerOrders, $providerGoods;

    /**
     * Эмитировать долгую обработку запроса, как в 1С, если true
     *
     * @var bool
     */
    private $useDelay = false;

    /**
     * HandlerResetOrder constructor
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
     * @param \Example\Client1C\RequestTypes\RequestResetOrder $request
     * @return \Example\Client1C\ResponseTypes\ResponseResetOrder
     */
    public function handle(RequestResetOrder $request)
    {
        // `status = 1` - отсутствует `orderUUID`
        if (empty($request->orderUUID)) {
            return new ResponseResetOrder(ResponseResetOrder::STATUS_EMPTY_ORDER_UUID);
        }

        if ($this->useDelay) {
            // Имитация долгого выполнения запроса, как в 1С
            $delay = mt_rand(self::DELAY_MIN, self::DELAY_MAX);
            sleep(min($delay, self::TIMEOUT));

            // `status = 6` - время ожидания старта выполнения задачи превышено
            if ($delay > self::TIMEOUT) {
                return new ResponseResetOrder(ResponseResetOrder::STATUS_TIME_IS_OUT);
            }
        }

        try {
            $order = $this->providerOrders->getOrder($request->orderUUID);

            // `status = 5` - ошибка проведения заказа
            // Внутренняя ошибка 1С

            // Разрезервируем товар
            foreach ($order->goods() as $good) {
                /** @var DumbOrderItem $good */
                try {
                    // Удаляем товар из заказа
                    $removed = $order->remove($good->goodUUID);
                    // Получаем провайдер данных товара и разрезервируем
                    $this->providerGoods->getGood($good->goodUUID)->release($removed);
                } catch (InvalidUUIDException $e) {
                    // Ignore
                } catch (GoodNotFoundException $e) {
                    // Ignore
                } catch (GoodToRemoveNotFoundInOrderException $e) {
                    // Ignore
                }
            }

            // Отменяем заказ
            $order->reset();

            // `status = 0` - все хорошо (заказ отменен)
            return new ResponseResetOrder(ResponseResetOrder::STATUS_SUCCESS);

        } catch (InvalidUUIDException $e) {
            // `status = 2` - некорректный `orderUUID`
            return new ResponseResetOrder(ResponseResetOrder::STATUS_INVALID_ORDER_UUID);
        } catch (OrderNotFoundException $e) {
            // `status = 3` - заказ с переданным `orderUUID` не найден
            return new ResponseResetOrder(ResponseResetOrder::STATUS_ORDER_NOT_FOUND);
        } catch (OrderConfirmedException $e) {
            // `status = 4` - заказ уже подтвержден
            return new ResponseResetOrder(ResponseResetOrder::STATUS_ORDER_CONFIRMED);
        } catch (OrderResetedException $e) {
            // `status = 7` - заказ уже удален
            return new ResponseResetOrder(ResponseResetOrder::STATUS_ORDER_RESETED);
        }
    }
}
