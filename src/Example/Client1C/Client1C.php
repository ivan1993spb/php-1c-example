<?php

namespace Example\Client1C;

use Example\Client1C\Exceptions\MethodInvocationException;
use Example\Client1C\RequestTypes\RequestAddGoodsToOrder;
use Example\Client1C\RequestTypes\RequestConfirmOrder;
use Example\Client1C\RequestTypes\RequestResetOrder;
use Example\Client1C\RequestTypes\RequestAddOrder;
use Example\Client1C\RequestTypes\RequestAddGoodToOrder;
use Example\Client1C\ResponseTypes\ResponseAddGoodsToOrder;
use Example\Client1C\ResponseTypes\ResponseAddGoodToOrder;
use Example\Client1C\ResponseTypes\ResponseAddOrder;
use Example\Client1C\ResponseTypes\OrderItem;
use Example\Client1C\ResponseTypes\GoodState;
use Example\Client1C\ResponseTypes\ResponseConfirmOrder;
use Example\Client1C\ResponseTypes\ResponseResetOrder;
use SoapClient;
use SoapFault;

/**
 * Class Client1C Клиент-обертка для SoapClient для взаимодействия с веб-сервисами 1С
 *
 * @package Example\Client1C
 */
class Client1C implements Client1CInterface
{
    /**
     * Название метода для добавления заказа
     *
     * @var string
     */
    const METHOD_ADD_ORDER = 'AddOrder';

    /**
     * Название метода для добавления товара
     *
     * @var string
     */
    const METHOD_ADD_GOOD_TO_ORDER = 'AddGoodToOrder';

    /**
     * Название метода для добавления товаров
     *
     * @var string
     */
    const METHOD_ADD_GOODS_TO_ORDER = 'AddGoodsToOrder';

    /**
     * Название метода для подтверждения заказа
     *
     * @var string
     */
    const METHOD_CONFIRM_ORDER = 'ConfirmOrder';

    /**
     * Название метода для отмены заказа
     *
     * @var string
     */
    const METHOD_RESET_ORDER = 'ResetOrder';

    /**
     * @var SoapClient
     */
    private $soapClient;

    /**
     * Client1C constructor
     *
     * @param string $wsdl Путь к WSDL файлу для генерации SOAP клиента
     * @param string $userAgent
     */
    public function __construct($wsdl, $userAgent = 'PHP-SOAP-CLIENT')
    {
        $this->soapClient = new SoapClient($wsdl, [
            'exceptions'         => true,
            'soap_version'       => SOAP_1_1,
            'cache_wsdl'         => WSDL_CACHE_NONE,
            'connection_timeout' => 30, // sec
            'user_agent'         => $userAgent,
            'features'           => SOAP_SINGLE_ELEMENT_ARRAYS,
            'keep_alive'         => false,
            'classmap'           => [
                'AddGoodToOrderResponse'            => ResponseAddGoodToOrder::class,
                'AddGoodsToOrderResponse'           => ResponseAddGoodsToOrder::class,
                'AddOrderResponse'                  => ResponseAddOrder::class,
                'ConfirmOrderResponse'              => ResponseConfirmOrder::class,
                'ResetOrderResponse'                => ResponseResetOrder::class,
                'AddGoodsToOrderResponseRowGoods'   => GoodState::class,
                'AddGoodToOrderResponseRow'         => OrderItem::class,
                'AddGoodsToOrderResponseRowOrder'   => OrderItem::class,
            ],
        ]);
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
            /** @var \Example\Client1C\ResponseTypes\ResponseAddOrder $response */
            $response = $this->soapClient->__soapCall(self::METHOD_ADD_ORDER, [$request])->return;
            return $response->attachTimeNow()->attachRequest($request);
        } catch (SoapFault $e) {
            throw new MethodInvocationException(self::METHOD_ADD_ORDER, 0, $e);
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
            /** @var \Example\Client1C\ResponseTypes\ResponseAddGoodToOrder $response */
            $response = $this->soapClient->__soapCall(self::METHOD_ADD_GOOD_TO_ORDER, [$request])->return;
            return $response->attachTimeNow()->attachRequest($request);
        } catch (SoapFault $e) {
            throw new MethodInvocationException(self::METHOD_ADD_GOOD_TO_ORDER, 0, $e);
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
            /** @var \Example\Client1C\ResponseTypes\ResponseAddGoodsToOrder $response */
            $response = $this->soapClient->__soapCall(self::METHOD_ADD_GOODS_TO_ORDER, [$request])->return;
            return $response->attachTimeNow()->attachRequest($request);
        } catch (SoapFault $e) {
            throw new MethodInvocationException(self::METHOD_ADD_GOODS_TO_ORDER, 0, $e);
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
            /** @var \Example\Client1C\ResponseTypes\ResponseConfirmOrder $response */
            $response = $this->soapClient->__soapCall(self::METHOD_CONFIRM_ORDER, [$request])->return;
            return $response->attachTimeNow()->attachRequest($request);
        } catch (SoapFault $e) {
            throw new MethodInvocationException(self::METHOD_CONFIRM_ORDER, 0, $e);
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
            /** @var \Example\Client1C\ResponseTypes\ResponseResetOrder $response */
            $response = $this->soapClient->__soapCall(self::METHOD_RESET_ORDER, [$request])->return;
            return $response->attachTimeNow()->attachRequest($request);
        } catch (SoapFault $e) {
            throw new MethodInvocationException(self::METHOD_RESET_ORDER, 0, $e);
        }
    }
}
