<?php

namespace Example\Client1C\ResponseTypes;

use Example\Client1C\Response;

/**
 * Class ResponseConfirmOrder
 *
 * @package Example\Client1C\ResponseTypes
 */
class ResponseConfirmOrder extends Response
{
    const STATUS_SUCCESS                = 0; // заказ подтвержден
    const STATUS_EMPTY_ORDER_UUID       = 1; // отсутствует `orderUUID`
    const STATUS_INVALID_ORDER_UUID     = 2; // некорректный `orderUUID`
    const STATUS_ORDER_NOT_FOUND        = 3; // заказ с переданным `orderUUID` не найден
    const STATUS_ORDER_CONFIRMED        = 4; // заказ уже подтвержден
    const STATUS_ORDER_PROCESSING_ERROR = 5; // ошибка проведения заказа
    const STATUS_TIME_IS_OUT            = 6; // время ожидания старта задания превышено
    const STATUS_ORDER_RESETED          = 7; // заказ уже удален
    const STATUS_EMPTY_ORDER            = 8; // невозможно подтвердить пустой заказ

    /**
     * @var int   $status         Status code
     * @var float $discountAmount Размер скидки заказа
     */
    public $status = -1, $discountAmount = -1;

    /**
     * ResponseConfirmOrder constructor
     *
     * @param int $status
     */
    public function __construct($status = -1, $discountAmount = 0)
    {
        $this->status = $status;
        $this->discountAmount = $discountAmount;
    }
}
