<?php

namespace Example\Client1C\ResponseTypes;

use Example\Client1C\Response;

/**
 * Class ResponseResetOrder
 *
 * @package Example\Client1C\ResponseTypes
 */
class ResponseResetOrder extends Response
{
    const STATUS_SUCCESS                = 0; // все хорошо (заказ отменен)
    const STATUS_EMPTY_ORDER_UUID       = 1; // отсутствует `orderUUID`
    const STATUS_INVALID_ORDER_UUID     = 2; // некорректный `orderUUID`
    const STATUS_ORDER_NOT_FOUND        = 3; // заказ с переданным `orderUUID` не найден
    const STATUS_ORDER_CONFIRMED        = 4; // заказ уже подтвержден
    const STATUS_ORDER_PROCESSING_ERROR = 5; // ошибка проведения заказа
    const STATUS_TIME_IS_OUT            = 6; // время ожидания старта задания превышено
    const STATUS_ORDER_RESETED          = 7; // заказ уже удален

    /**
     * @var int $status Status code
     */
    public $status = -1;

    /**
     * ResponseResetOrder constructor
     *
     * @param int $status
     */
    public function __construct($status = -1)
    {
        $this->status = $status;
    }
}
