<?php

namespace Example\Client1C\ResponseTypes;

use Example\Client1C\Response;

/**
 * Class ResponseAddOrder
 *
 * @package Example\Client1C\ResponseTypes
 */
class ResponseAddOrder extends Response
{
    const STATUS_SUCCESS                          = 0; // все хорошо
    const STATUS_EMPTY_CLIENT_UUID                = 1; // `clientUUID` не был передан
    const STATUS_INVALID_CLIENT_UUID              = 2; // `clientUUID` не корректен
    const STATUS_CLIENT_NOT_FOUND                 = 3; // клиент с `clientUUID` не найден
    const STATUS_CLIENT_AGREEMENT_DOES_NOT_EXISTS = 4; // невозможно определить соглашение
    const STATUS_ERROR_WRITING_ORDER              = 5; // ошибка записи заказа

    const PAY_TYPE_PREPAY_FULL = 0; // предоплата 100%
    const PAY_TYPE_PREPAY_HALF = 1; // предоплата 50%
    const PAY_TYPE_CONTRACT    = 2; // оплата согласно условиям договора

    const DELIVERY_TYPE_PICKUP   = 0; // самовывоз. Этот тип вернется в случае ошибки
    const DELIVERY_TYPE_DELIVERY = 1; // доставка

    /**
     * @var string $orderUUID    Order UUID
     * @var string $orderID      Order ID
     * @var int    $VAT          VAT
     * @var int    $payType      Pay type
     * @var int    $deliveryType Delivery type
     * @var int    $status       Status code
     */
    public $orderUUID, $orderID, $VAT = -1, $payType = -1, $deliveryType = -1, $status = -1;

    /**
     * ResponseAddOrder constructor
     *
     * @param int         $status
     * @param string|null $orderUUID
     * @param string|null $orderID
     * @param int         $VAT
     * @param int         $payType
     * @param int         $deliveryType
     */
    public function __construct($status = -1, $orderUUID = null, $orderID = null, $VAT = -1, $payType = -1, $deliveryType = -1)
    {
        $this->orderUUID = $orderUUID;
        $this->orderID = $orderID;
        $this->VAT = $VAT;
        $this->payType = $payType;
        $this->deliveryType = $deliveryType;
        $this->status = $status;
    }
}
