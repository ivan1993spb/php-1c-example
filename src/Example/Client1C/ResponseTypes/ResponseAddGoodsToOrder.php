<?php

namespace Example\Client1C\ResponseTypes;

use Example\Client1C\Response;

/**
 * Class ResponseAddGoodsToOrder
 *
 * @package Example\Client1C\ResponseTypes
 */
class ResponseAddGoodsToOrder extends Response
{
    const STATUS_SUCCESS                 = 0;  // все хорошо
    const STATUS_EMPTY_ORDER_UUID        = 1;  // `orderUUID` не передан
    const STATUS_INVALID_ORDER_UUID      = 2;  // `orderUUID` не корректен
    const STATUS_ORDER_NOT_FOUND         = 3;  // заказ с UUID `orderUUID` не найден
    const STATUS_GOOD_LIST_NOT_PASSED    = 4;  // список товара для добавления `goods` не был передан
    const STATUS_EMPTY_GOOD_LIST         = 5;  // список товара для добавления `goods` пустой
    const STATUS_EXECUTED_NOT_ENOUGH     = 6;  // запрос выполнен частично
    const STATUS_ORDER_CONFIRMED         = 7;  // заказ уже подтвержден
    const STATUS_TRANSACTION_TIME_IS_OUT = 8;  // превышен лемит ожидания транзакции
    const STATUS_ORDER_PROCESSING_ERROR  = 9;  // ошибка проведения заказа
    const STATUS_TIME_IS_OUT             = 10; // время ожидания старта задания превышено
    const STATUS_ORDER_RESETED           = 11; // заказ уже удален

    /**
     * Goods created in order
     *
     * @var \Example\Client1C\ResponseTypes\GoodState[]
     */
    public $goods = [];

    /**
     * Goods in the order
     *
     * @var \Example\Client1C\ResponseTypes\OrderItem[]
     */
    public $order = [];

    /*
     * @var float $discountAmount Размер скидки заказа
     * @var int   $status         Status code
     */
    public $discountAmount = 0, $status = -1;

    /**
     * ResponseAddGoodsToOrder constructor
     *
     * @param int   $status
     * @param array $goods
     * @param array $order
     * @param float $discountAmount
     */
    public function __construct($status = -1, $goods = [], $order = [], $discountAmount = 0.0)
    {
        $this->goods = $goods;
        $this->order = $order;
        $this->status = $status;
        $this->discountAmount = $discountAmount;
    }
}
