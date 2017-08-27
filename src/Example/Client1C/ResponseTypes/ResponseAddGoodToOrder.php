<?php

namespace Example\Client1C\ResponseTypes;

use Example\Client1C\Response;

/**
 * Class ResponseAddGoodToOrder
 *
 * @package Example\Client1C\ResponseTypes
 */
class ResponseAddGoodToOrder extends Response
{
    const STATUS_SUCCESS                 = 0;  // все хорошо
    const STATUS_EMPTY_ORDER_UUID        = 1;  // `orderUUID` не переданн
    const STATUS_INVALID_ORDER_UUID      = 2;  // `orderUUID` не корректный
    const STATUS_ORDER_NOT_FOUND         = 3;  // заказ c `orderUUID` не найден
    const STATUS_EMPTY_GOOD_UUID         = 4;  // `goodUUID` не был передан
    const STATUS_INVALID_GOOD_UUID       = 5;  // `goodUUID` не корректный
    const STATUS_GOOD_NOT_FOUND          = 6;  // товар с `goodUUID` не найден
    const STATUS_EMPTY_COUNT             = 7;  // `count` не был переданн
    const STATUS_INVALID_COUNT           = 8;  // `count` не корректный (отрицательный)
    const STATUS_ORDER_CONFIRMED         = 9;  // заказ уже подтвержден
    const STATUS_ADDED_NOT_ENOUGH        = 10; // товар добавлен в корзину частично (`< count`)
    const STATUS_GOOD_HAVE_NOT           = 11; // товар не может быть добавлен из-за нулевого остатка
    const STATUS_GOOD_NOT_FOUND_IN_ORDER = 12; // товар для удаления не найден в заказе
    const STATUS_TRANSACTION_TIME_IS_OUT = 13; // превышен лемит ожидания транзакции
    const STATUS_ORDER_PROCESSING_ERROR  = 14; // ошибка проведения заказа
    const STATUS_REQUEST_IS_OUT_OF_DATE  = 15; // запрос на изменение устарел и не будет выполнен
    const STATUS_TIME_IS_OUT             = 16; // время ожидания старта задания превышено
    const STATUS_ORDER_RESETED           = 17; // заказ уже отменен

    /**
     * @var string $goodUUID       Good's UUID
     * @var int    $count          Count of created goods
     * @var int    $required       How many goods was required
     * @var float  $discountAmount Размер скидки заказа
     * @var int    $status         Status code
     */
    public $goodUUID, $count = -1, $required = -1, $discountAmount = 0, $status = -1;

    /**
     * Goods in the order
     *
     * @var \Example\Client1C\ResponseTypes\OrderItem[]
     */
    public $order = [];

    /**
     * ResponseAddGoodToOrder constructor
     *
     * @param int         $status
     * @param string|null $goodUUID
     * @param int         $count
     * @param int         $required
     * @param array       $order
     */
    public function __construct($status = -1, $goodUUID = null, $count = -1, $required = -1, $order = [], $discountAmount = 0)
    {
        $this->goodUUID = $goodUUID;
        $this->count = $count;
        $this->required = $required;
        $this->order = $order;
        $this->discountAmount = $discountAmount;
        $this->status = $status;
    }
}
