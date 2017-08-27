<?php

namespace Example\Client1C\Testing\Handlers;

use Example\Client1C\RequestTypes\RequestConfirmOrder;
use Example\Client1C\ResponseTypes\ResponseConfirmOrder;
use Example\Client1C\Testing\Exceptions\ConfirmEmptyOrderException;
use Example\Client1C\Testing\Exceptions\InvalidUUIDException;
use Example\Client1C\Testing\Exceptions\OrderConfirmedException;
use Example\Client1C\Testing\Exceptions\OrderNotFoundException;
use Example\Client1C\Testing\Exceptions\OrderResetedException;
use Example\Client1C\Testing\ProviderInterfaces\ProviderOrders;

/**
 * Class HandlerConfirmOrder
 *
 * @package Example\Client1C\Testing\Handlers
 */
class HandlerConfirmOrder
{
    const DELAY_MIN = 7;
    const DELAY_MAX = 11;

    const TIMEOUT = 11;

    /**
     * @var \Example\Client1C\Testing\ProviderInterfaces\ProviderOrders $providerOrders
     */
    private $providerOrders;

    /**
     * Эмитировать долгую обработку запроса, как в 1С, если true
     *
     * @var bool
     */
    private $useDelay = false;

    /**
     * HandlerConfirmOrder constructor.
     *
     * @param \Example\Client1C\Testing\ProviderInterfaces\ProviderOrders $providerOrders
     * @param bool                                                               $useDelay
     */
    public function __construct(ProviderOrders $providerOrders, $useDelay = false)
    {
        $this->providerOrders = $providerOrders;
        $this->useDelay = $useDelay;
    }

    /**
     * @param \Example\Client1C\RequestTypes\RequestConfirmOrder $request
     * @return \Example\Client1C\ResponseTypes\ResponseConfirmOrder
     */
    public function handle(RequestConfirmOrder $request)
    {
        // `status = 1` - отсутствует `orderUUID`
        if (empty($request->orderUUID)) {
            return new ResponseConfirmOrder(ResponseConfirmOrder::STATUS_EMPTY_ORDER_UUID, 0);
        }

        if ($this->useDelay) {
            // Имитация долгого выполнения запроса, как в 1С
            $delay = mt_rand(self::DELAY_MIN, self::DELAY_MAX);
            sleep(min($delay, self::TIMEOUT));

            // `status = 6` - время ожидания старта выполнения задачи превышено
            if ($delay > self::TIMEOUT) {
                return new ResponseConfirmOrder(ResponseConfirmOrder::STATUS_TIME_IS_OUT, 0);
            }
        }

        try {
            $order = $this->providerOrders->getOrder($request->orderUUID);

            // `status = 5` - ошибка проведения заказа
            // Внутренняя ошибка 1С

            // Подтверждаем заказ
            $order->confirm();

            // `status = 0` - все хорошо, заказ подтвержден
            return new ResponseConfirmOrder(ResponseConfirmOrder::STATUS_SUCCESS, 0);

        } catch (InvalidUUIDException $e) {
            // `status = 2` - некорректный `orderUUID`
            return new ResponseConfirmOrder(ResponseConfirmOrder::STATUS_INVALID_ORDER_UUID, 0);
        } catch (OrderNotFoundException $e) {
            // `status = 3` - заказ с переданным `orderUUID` не найден
            return new ResponseConfirmOrder(ResponseConfirmOrder::STATUS_ORDER_NOT_FOUND, 0);
        } catch (OrderConfirmedException $e) {
            // `status = 4` - заказ уже подтвержден
            return new ResponseConfirmOrder(ResponseConfirmOrder::STATUS_ORDER_CONFIRMED, 0);
        } catch (OrderResetedException $e) {
            // `status = 7` - заказ уже удален
            return new ResponseConfirmOrder(ResponseConfirmOrder::STATUS_ORDER_RESETED, 0);
        } catch (ConfirmEmptyOrderException $e) {
            // `status = 8` - заказ пустой: невозможно подтвердить пустой заказ
            return new ResponseConfirmOrder(ResponseConfirmOrder::STATUS_EMPTY_ORDER, 0);
        }
    }
}
