<?php

use Example\Client1C\RequestTypes\RequestAddGoodsToOrder;
use Example\Client1C\RequestTypes\RequestAddGoodToOrder;
use Example\Client1C\RequestTypes\RequestAddOrder;
use Example\Client1C\RequestTypes\RequestConfirmOrder;
use Example\Client1C\RequestTypes\RequestResetOrder;
use Example\Client1C\RequestTypes\GoodItem;
use Example\Client1C\ResponseTypes\GoodState;
use Example\Client1C\ResponseTypes\ResponseAddGoodsToOrder;
use Example\Client1C\ResponseTypes\ResponseAddGoodToOrder;
use Example\Client1C\ResponseTypes\ResponseAddOrder;
use Example\Client1C\ResponseTypes\ResponseConfirmOrder;
use Example\Client1C\ResponseTypes\ResponseResetOrder;
use PHPUnit\Framework\TestCase;

/**
 * Class Client1CTestBase
 */
abstract class Client1CTestBase extends TestCase
{
    // Корректный UUID
    protected static $validUUID = '00000000-0000-0000-0000-000000000000';

    // UUID несуществующего клиента
    protected static $clientUUIDNotFound = '00000000-0000-0000-0001-000000000000';

    // UUID клиента не имеющего соглашение
    protected static $clientUUIDHasNotAgreement = '';

    // UUID существующего клиента
    protected static $clientUUIDExists = '';

    // Некорректный UUID
    protected static $invalidUUID = 'invalid uuid';

    // UUID несуществующего заказа
    protected static $orderUUIDNotFound = '00000000-0000-0000-0002-000000000000';

    // UUID несуществующей номенклатуры
    protected static $goodUUIDNotFound = '00000000-0000-0000-0003-000000000000';

    // UUID товара, которого очень много на складе
    protected static $goodUUIDMany = '';

    // UUID товара, которого > 0 и < 10 штук на складе
    protected static $goodUUIDMoreThen_0_lessThen_10 = '';

    // UUID товара, которого 0 штук на складе
    protected static $goodUUIDHaveNot = '';

    // UUID существующего товара
    protected static $goodUUIDExists = '';

    // UUID товаров, которых > 0 на складе
    protected static $goodUUIDMoreThen_0_1 = '';
    protected static $goodUUIDMoreThen_0_2 = '';
    protected static $goodUUIDMoreThen_0_3 = '';
    protected static $goodUUIDMoreThen_0_4 = '';

    /**
     * @return \Example\Client1C\Client1CInterface
     */
    abstract protected function getClient();

    /**
     * @large
     */
    final public function testAddOrder()
    {
        $client = $this->getClient();

        // `status = 1` - `clientUUID` не был передан
        $request = new RequestAddOrder(self::$validUUID);
        $request->clientUUID = '';
        $response = $client->addOrder($request);
        $this->assertEquals(ResponseAddOrder::STATUS_EMPTY_CLIENT_UUID, $response->status);

        // `status = 2` - `clientUUID` не корректен
        $request = new RequestAddOrder(self::$validUUID);
        $request->clientUUID = self::$invalidUUID;
        $response = $client->addOrder($request);
        $this->assertEquals(ResponseAddOrder::STATUS_INVALID_CLIENT_UUID, $response->status);

        // `status = 3` - клиент с `clientUUID` не найден
        $response = $client->addOrder(new RequestAddOrder(self::$clientUUIDNotFound));
        $this->assertEquals(ResponseAddOrder::STATUS_CLIENT_NOT_FOUND, $response->status);

        // `status = 4` - невозможно определить соглашение
        $response = $client->addOrder(new RequestAddOrder(self::$clientUUIDHasNotAgreement));
        $this->assertEquals(ResponseAddOrder::STATUS_CLIENT_AGREEMENT_DOES_NOT_EXISTS, $response->status);

        // `status = 5` - ошибка записи заказа

        // `status = 0` - все хорошо
        $response = $client->addOrder(new RequestAddOrder(self::$clientUUIDExists));
        $this->assertEquals(ResponseAddOrder::STATUS_SUCCESS, $response->status);

        // Сотрем тестовый заказ
        $response = $client->resetOrder(new RequestResetOrder($response->orderUUID));
        $this->assertEquals(ResponseResetOrder::STATUS_SUCCESS, $response->status, "cannot reset test order");
    }

    /**
     * @large
     */
    final public function testResetOrder()
    {
        $client = $this->getClient();

        // `status = 1` - отсутствует `orderUUID`
        $request = new RequestResetOrder(self::$validUUID);
        $request->orderUUID = '';
        $response = $client->resetOrder($request);
        $this->assertEquals(ResponseResetOrder::STATUS_EMPTY_ORDER_UUID, $response->status);

        // `status = 2` - некорректный `orderUUID`
        $request = new RequestResetOrder(self::$validUUID);
        $request->orderUUID = self::$invalidUUID;
        $response = $client->resetOrder($request);
        $this->assertEquals(ResponseResetOrder::STATUS_INVALID_ORDER_UUID, $response->status);

        // `status = 3` - заказ с переданным `orderUUID` не найден
        $response = $client->resetOrder(new RequestResetOrder(self::$orderUUIDNotFound));
        $this->assertEquals(ResponseResetOrder::STATUS_ORDER_NOT_FOUND, $response->status);

        // `status = 4` - заказ уже подтвержден
        $response = $client->addOrder(new RequestAddOrder(self::$clientUUIDExists));
        $this->assertEquals(ResponseAddOrder::STATUS_SUCCESS, $response->status, "cannot add test order");
        $orderUUID = $response->orderUUID;
        $response = $client->addGoodToOrder(new RequestAddGoodToOrder($orderUUID, self::$goodUUIDMany, 1));
        $this->assertEquals(ResponseAddGoodToOrder::STATUS_SUCCESS, $response->status, "cannot add good to test order");
        $response = $client->confirmOrder(new RequestConfirmOrder($orderUUID));
        $this->assertEquals(ResponseConfirmOrder::STATUS_SUCCESS, $response->status, "cannot confirm test order");
        $response = $client->resetOrder(new RequestResetOrder($orderUUID));
        $this->assertEquals(ResponseResetOrder::STATUS_ORDER_CONFIRMED, $response->status);

        // `status = 5` - ошибка проведения заказа

        // `status = 6` - максимальное время соединения превышено, при этом задача не начала выполняться

        // `status = 7` - заказ уже удален
        $response = $client->addOrder(new RequestAddOrder(self::$clientUUIDExists));
        $this->assertEquals(ResponseAddOrder::STATUS_SUCCESS, $response->status, "cannot add test order");
        $orderUUID = $response->orderUUID;
        $response = $client->resetOrder(new RequestResetOrder($orderUUID));
        $this->assertEquals(ResponseResetOrder::STATUS_SUCCESS, $response->status, "cannot reset test order");
        $response = $client->resetOrder(new RequestResetOrder($orderUUID));
        $this->assertEquals(ResponseResetOrder::STATUS_ORDER_RESETED, $response->status);

        // `status = 0` - все хорошо (заказ отменен)
        $response = $client->addOrder(new RequestAddOrder(self::$clientUUIDExists));
        $this->assertEquals(ResponseAddOrder::STATUS_SUCCESS, $response->status, "cannot add test order");
        $orderUUID = $response->orderUUID;
        $response = $client->resetOrder(new RequestResetOrder($orderUUID));
        $this->assertEquals(ResponseResetOrder::STATUS_SUCCESS, $response->status);
    }

    /**
     * @large
     */
    final public function testConfirmOrder()
    {
        $client = $this->getClient();

        // `status = 1` - отсутствует `orderUUID`
        $request = new RequestConfirmOrder(self::$validUUID);
        $request->orderUUID = '';
        $response = $client->confirmOrder($request);
        $this->assertEquals(ResponseConfirmOrder::STATUS_EMPTY_ORDER_UUID, $response->status);

        // `status = 2` - некорректный `orderUUID`
        $request = new RequestConfirmOrder(self::$validUUID);
        $request->orderUUID = self::$invalidUUID;
        $response = $client->confirmOrder($request);
        $this->assertEquals(ResponseConfirmOrder::STATUS_INVALID_ORDER_UUID, $response->status);

        // `status = 3` - заказ с переданным `orderUUID` не найден
        $response = $client->confirmOrder(new RequestConfirmOrder(self::$orderUUIDNotFound));
        $this->assertEquals(ResponseConfirmOrder::STATUS_ORDER_NOT_FOUND, $response->status);

        // `status = 4` - заказ уже подтвержден
        $response = $client->addOrder(new RequestAddOrder(self::$clientUUIDExists));
        $this->assertEquals(ResponseAddOrder::STATUS_SUCCESS, $response->status, "cannot add test order");
        $orderUUID = $response->orderUUID;
        $response = $client->addGoodToOrder(new RequestAddGoodToOrder($orderUUID, self::$goodUUIDMany, 1));
        $this->assertEquals(ResponseAddGoodToOrder::STATUS_SUCCESS, $response->status, "cannot add good to test order");
        $response = $client->confirmOrder(new RequestConfirmOrder($orderUUID));
        $this->assertEquals(ResponseConfirmOrder::STATUS_SUCCESS, $response->status, "cannot confirm test order");
        $response = $client->confirmOrder(new RequestConfirmOrder($orderUUID));
        $this->assertEquals(ResponseConfirmOrder::STATUS_ORDER_CONFIRMED, $response->status);

        // `status = 5` - ошибка проведения заказа

        // `status = 6` - максимальное время соединения превышено, при этом задача не начала выполняться

        // `status = 7` - заказ уже удален
        $response = $client->addOrder(new RequestAddOrder(self::$clientUUIDExists));
        $this->assertEquals(ResponseAddOrder::STATUS_SUCCESS, $response->status, "cannot add test order");
        $orderUUID = $response->orderUUID;
        $response = $client->resetOrder(new RequestResetOrder($orderUUID));
        $this->assertEquals(ResponseResetOrder::STATUS_SUCCESS, $response->status, "cannot reset test order");
        $response = $client->confirmOrder(new RequestConfirmOrder($orderUUID));
        $this->assertEquals(ResponseConfirmOrder::STATUS_ORDER_RESETED, $response->status);

        // `status = 8` - заказ пустой: невозможно подтвердить пустой заказ
        $response = $client->addOrder(new RequestAddOrder(self::$clientUUIDExists));
        $this->assertEquals(ResponseAddOrder::STATUS_SUCCESS, $response->status, "cannot add test order");
        $orderUUID = $response->orderUUID;
        $response = $client->confirmOrder(new RequestConfirmOrder($orderUUID));
        $this->assertEquals(ResponseConfirmOrder::STATUS_EMPTY_ORDER, $response->status);

        // `status = 0` - все хорошо, заказ подтвержден
        $response = $client->addOrder(new RequestAddOrder(self::$clientUUIDExists));
        $this->assertEquals(ResponseAddOrder::STATUS_SUCCESS, $response->status, "cannot add test order");
        $orderUUID = $response->orderUUID;
        $response = $client->addGoodToOrder(new RequestAddGoodToOrder($orderUUID, self::$goodUUIDMany, 1));
        $this->assertEquals(ResponseAddGoodToOrder::STATUS_SUCCESS, $response->status, "cannot add good to test order");
        $response = $client->confirmOrder(new RequestConfirmOrder($orderUUID));
        $this->assertEquals(ResponseConfirmOrder::STATUS_SUCCESS, $response->status);
    }

    /**
     * @large
     */
    final public function testAddGoodToOrder()
    {
        $client = $this->getClient();

        // `status = 1` - `orderUUID` не переданн
        $request = new RequestAddGoodToOrder(self::$validUUID, self::$validUUID, 0);
        $request->orderUUID = '';
        $response = $client->addGoodToOrder($request);
        $this->assertEquals(ResponseAddGoodToOrder::STATUS_EMPTY_ORDER_UUID, $response->status);

        // `status = 2` - `orderUUID` не корректный
        $request = new RequestAddGoodToOrder(self::$validUUID, self::$validUUID, 0);
        $request->orderUUID = self::$invalidUUID;
        $response = $client->addGoodToOrder($request);
        $this->assertEquals(ResponseAddGoodToOrder::STATUS_INVALID_ORDER_UUID, $response->status);

        // `status = 3` - заказ c `orderUUID` не найден
        $response = $client->addGoodToOrder(new RequestAddGoodToOrder(self::$orderUUIDNotFound, self::$validUUID, 0));
        $this->assertEquals(ResponseAddGoodToOrder::STATUS_ORDER_NOT_FOUND, $response->status);

        // Добавляем тестовый заказ
        $response = $client->addOrder(new RequestAddOrder(self::$clientUUIDExists));
        $this->assertEquals(ResponseAddOrder::STATUS_SUCCESS, $response->status, "cannot add test order");
        $orderUUID = $response->orderUUID;

        // `status = 4` - `goodUUID` не был передан
        $request = new RequestAddGoodToOrder($orderUUID, self::$validUUID, 0);
        $request->goodUUID = '';
        $response = $client->addGoodToOrder($request);
        $this->assertEquals(ResponseAddGoodToOrder::STATUS_EMPTY_GOOD_UUID, $response->status);

        // `status = 5` - `goodUUID` не корректный
        $request = new RequestAddGoodToOrder($orderUUID, self::$validUUID, 0);
        $request->goodUUID = self::$invalidUUID;
        $response = $client->addGoodToOrder($request);
        $this->assertEquals(ResponseAddGoodToOrder::STATUS_INVALID_GOOD_UUID, $response->status);

        // `status = 6` - товар с `goodUUID` не найден
        $response = $client->addGoodToOrder(new RequestAddGoodToOrder($orderUUID, self::$goodUUIDNotFound, 0));
        $this->assertEquals(ResponseAddGoodToOrder::STATUS_GOOD_NOT_FOUND, $response->status);

        // `status = 7` - `count` не был переданн
        // empty string == 0
        // Если php передает пустую строку, то она интерпретируется как 0

        // `status = 8` - `count` не корректный (отрицательный)
        $request = new RequestAddGoodToOrder($orderUUID, self::$goodUUIDMany, 0);
        $request->count = -1;
        $response = $client->addGoodToOrder($request);
        $this->assertEquals(ResponseAddGoodToOrder::STATUS_INVALID_COUNT, $response->status);

        // `status = 9` - заказ уже подтвержден
        $response = $client->addGoodToOrder(new RequestAddGoodToOrder($orderUUID, self::$goodUUIDMany, 1));
        $this->assertEquals(ResponseAddGoodToOrder::STATUS_SUCCESS, $response->status, "cannot add good to test order");
        $response = $client->confirmOrder(new RequestConfirmOrder($orderUUID));
        $this->assertEquals(ResponseConfirmOrder::STATUS_SUCCESS, $response->status, "cannot confirm test order");
        $response = $client->addGoodToOrder(new RequestAddGoodToOrder($orderUUID, self::$goodUUIDMany, 1));
        $this->assertEquals(ResponseAddGoodToOrder::STATUS_ORDER_CONFIRMED, $response->status);

        // Добавляем тестовый заказ
        $response = $client->addOrder(new RequestAddOrder(self::$clientUUIDExists));
        $this->assertEquals(ResponseAddOrder::STATUS_SUCCESS, $response->status, "cannot add test order");
        $orderUUID = $response->orderUUID;

        // `status = 10` - товар добавлен в корзину частично (`< count`)
        $response = $client->addGoodToOrder(new RequestAddGoodToOrder($orderUUID, self::$goodUUIDMoreThen_0_lessThen_10, 15));
        $this->assertEquals(ResponseAddGoodToOrder::STATUS_ADDED_NOT_ENOUGH, $response->status);

        // `status = 11` - товар не может быть добавлен из-за нулевого остатка
        $response = $client->addGoodToOrder(new RequestAddGoodToOrder($orderUUID, self::$goodUUIDHaveNot, 15));
        $this->assertEquals(ResponseAddGoodToOrder::STATUS_GOOD_HAVE_NOT, $response->status);

        // `status = 12` - товар для удаления не найден в заказе
        $response = $client->addGoodToOrder(new RequestAddGoodToOrder($orderUUID, self::$goodUUIDExists, 0));
        $this->assertEquals(ResponseAddGoodToOrder::STATUS_GOOD_NOT_FOUND_IN_ORDER, $response->status);
        // Добавляем и удаляем позицию
        $response = $client->addGoodToOrder(new RequestAddGoodToOrder($orderUUID, self::$goodUUIDMany, 1));
        $this->assertEquals(ResponseAddGoodToOrder::STATUS_SUCCESS, $response->status);
        $response = $client->addGoodToOrder(new RequestAddGoodToOrder($orderUUID, self::$goodUUIDMany, 0));
        $this->assertEquals(ResponseAddGoodToOrder::STATUS_SUCCESS, $response->status);

        // Затрем тестовый заказ
        $response = $client->resetOrder(new RequestResetOrder($orderUUID));
        $this->assertEquals(ResponseResetOrder::STATUS_SUCCESS, $response->status, "cannot reset test order");

        // `status = 13` - превышен лемит ожидания транзакции

        // `status = 14` - ошибка проведения заказа

        // `status = 15` - запрос на изменение устарел и не будет выполнен (по этой номенклатуре и заказу
        // поступил запрос с более поздней датой)

        // `status = 16` - максимальное время соединения превышено, при этом задача не начала выполняться

        // `status = 17` - заказ уже отменен
        $response = $client->addOrder(new RequestAddOrder(self::$clientUUIDExists));
        $this->assertEquals(ResponseAddOrder::STATUS_SUCCESS, $response->status, "cannot add test order");
        $orderUUID = $response->orderUUID;
        $response = $client->resetOrder(new RequestResetOrder($orderUUID));
        $this->assertEquals(ResponseResetOrder::STATUS_SUCCESS, $response->status, "cannot reset test order");
        $response = $client->addGoodToOrder(new RequestAddGoodToOrder($orderUUID, self::$goodUUIDMany, 1));
        $this->assertEquals(ResponseAddGoodToOrder::STATUS_ORDER_RESETED, $response->status);

        // `status = 0` - все хорошо
        // Добавим заказ
        $response = $client->addOrder(new RequestAddOrder(self::$clientUUIDExists));
        $this->assertEquals(ResponseAddOrder::STATUS_SUCCESS, $response->status, "cannot add test order");
        $orderUUID = $response->orderUUID;
        // Добавим несколько позиций и проверим все ли они есть в заказе
        $response = $client->addGoodToOrder(new RequestAddGoodToOrder($orderUUID, self::$goodUUIDMany, 1));
        $this->assertEquals(ResponseAddGoodToOrder::STATUS_SUCCESS, $response->status);
        $this->assertCount(1, $response->order);
        $response = $client->addGoodToOrder(new RequestAddGoodToOrder($orderUUID, self::$goodUUIDMoreThen_0_1, 1));
        $this->assertEquals(ResponseAddGoodToOrder::STATUS_SUCCESS, $response->status);
        $this->assertCount(2, $response->order);
        $response = $client->addGoodToOrder(new RequestAddGoodToOrder($orderUUID, self::$goodUUIDMoreThen_0_2, 1));
        $this->assertEquals(ResponseAddGoodToOrder::STATUS_SUCCESS, $response->status);
        $this->assertCount(3, $response->order);
        $response = $client->addGoodToOrder(new RequestAddGoodToOrder($orderUUID, self::$goodUUIDMoreThen_0_3, 1));
        $this->assertEquals(ResponseAddGoodToOrder::STATUS_SUCCESS, $response->status);
        $this->assertCount(4, $response->order);
        $response = $client->addGoodToOrder(new RequestAddGoodToOrder($orderUUID, self::$goodUUIDMoreThen_0_4, 1));
        $this->assertEquals(ResponseAddGoodToOrder::STATUS_SUCCESS, $response->status);
        $this->assertCount(5, $response->order);

        // Отменяем тестовый заказ
        $response = $client->resetOrder(new RequestResetOrder($orderUUID));
        $this->assertEquals(ResponseResetOrder::STATUS_SUCCESS, $response->status, "cannot reset test order");
    }

    /**
     * @large
     */
    final public function testAddGoodsToOrder()
    {
        $client = $this->getClient();

        // 1. Проверка общих статусов запроса

        // `status = 1` - `orderUUID` не передан
        $request = new RequestAddGoodsToOrder(self::$validUUID);
        $request->orderUUID = '';
        $response = $client->addGoodsToOrder($request);
        $this->assertEquals(ResponseAddGoodsToOrder::STATUS_EMPTY_ORDER_UUID, $response->status);

        // `status = 2` - `orderUUID` не корректен
        $request = new RequestAddGoodsToOrder(self::$validUUID);
        $request->orderUUID = self::$invalidUUID;
        $response = $client->addGoodsToOrder($request);
        $this->assertEquals(ResponseAddGoodsToOrder::STATUS_INVALID_ORDER_UUID, $response->status);

        // `status = 3` - заказ с UUID `orderUUID` не найден
        $response = $client->addGoodsToOrder(new RequestAddGoodsToOrder(self::$orderUUIDNotFound));
        $this->assertEquals(ResponseAddGoodsToOrder::STATUS_ORDER_NOT_FOUND, $response->status);

        // Добавляем тестовый заказ
        $response = $client->addOrder(new RequestAddOrder(self::$clientUUIDExists));
        $this->assertEquals(ResponseAddOrder::STATUS_SUCCESS, $response->status, "cannot add test order");
        $orderUUID = $response->orderUUID;

        // `status = 4` - список товара для добавления `goods` не был передан
        // Это невозможно проверить

        // `status = 5` - список товара для добавления `goods` пустой
        $response = $client->addGoodsToOrder(new RequestAddGoodsToOrder($orderUUID));
        $this->assertEquals(ResponseAddGoodsToOrder::STATUS_EMPTY_GOOD_LIST, $response->status);

        // `status = 6` - запрос выполнен частично: добавлены не все запрошенные позиции в том
        // количестве, которое требовалось из-за того, что не хватило остатков
        $request = (new RequestAddGoodsToOrder($orderUUID))
            ->addGood(new GoodItem(self::$goodUUIDMoreThen_0_1, 1))
            ->addGood(new GoodItem(self::$goodUUIDMoreThen_0_2, 1))
            ->addGood(new GoodItem(self::$goodUUIDMoreThen_0_3, 1))
            ->addGood(new GoodItem(self::$goodUUIDMoreThen_0_4, 1))
            ->addGood(new GoodItem(self::$goodUUIDMoreThen_0_lessThen_10, 20));
        $response = $client->addGoodsToOrder($request);
        $this->assertEquals(ResponseAddGoodsToOrder::STATUS_EXECUTED_NOT_ENOUGH, $response->status);
        $this->assertCount(5, $response->order);
        $this->assertCount(5, $response->goods);

        // Проверим количество товаров в ответе и в заказе
        $request = (new RequestAddGoodsToOrder($orderUUID))
            ->addGood(new GoodItem(self::$goodUUIDMoreThen_0_1, 0))
            ->addGood(new GoodItem(self::$goodUUIDMoreThen_0_2, 0));
        $response = $client->addGoodsToOrder($request);
        $this->assertEquals(ResponseAddGoodsToOrder::STATUS_SUCCESS, $response->status);
        // Удаляли 2 позиции
        $this->assertCount(2, $response->goods);
        // Осталось в заказе 3 позиции
        $this->assertCount(3, $response->order);

        // Попытаемся добавить товар, которого нет на остатках
        $request = new RequestAddGoodsToOrder(
            $orderUUID, [
            new GoodItem(self::$goodUUIDMoreThen_0_1, 1),
            new GoodItem(self::$goodUUIDMoreThen_0_2, 1),
            new GoodItem(self::$goodUUIDHaveNot, 1),
        ]
        );
        $response = $client->addGoodsToOrder($request);
        $this->assertEquals(ResponseAddGoodsToOrder::STATUS_EXECUTED_NOT_ENOUGH, $response->status);
        $this->assertCount(3, $response->goods);
        $this->assertCount(5, $response->order);

        // Отмена тестового заказа
        $response = $client->resetOrder(new RequestResetOrder($orderUUID));
        $this->assertEquals(ResponseResetOrder::STATUS_SUCCESS, $response->status, "cannot reset test order");

        // Добавляем тестовый заказ
        $response = $client->addOrder(new RequestAddOrder(self::$clientUUIDExists));
        $this->assertEquals(ResponseAddOrder::STATUS_SUCCESS, $response->status, "cannot add test order");
        $orderUUID = $response->orderUUID;

        // `status = 7` - заказ уже подтвержден
        $request = (new RequestAddGoodsToOrder($orderUUID))
            ->addGood(new GoodItem(self::$goodUUIDMany, 1));
        $response = $client->addGoodsToOrder($request);
        $this->assertEquals(ResponseAddGoodsToOrder::STATUS_SUCCESS, $response->status);
        $this->assertCount(1, $response->goods);
        $this->assertCount(1, $response->order);
        $response = $client->confirmOrder(new RequestConfirmOrder($orderUUID));
        $this->assertEquals(ResponseConfirmOrder::STATUS_SUCCESS, $response->status, "cannot confirm test order");
        $request = (new RequestAddGoodsToOrder($orderUUID))
            ->addGood(new GoodItem(self::$goodUUIDMany, 0));
        $response = $client->addGoodsToOrder($request);
        $this->assertEquals(ResponseAddGoodsToOrder::STATUS_ORDER_CONFIRMED, $response->status);

        // `status = 8` - превышен лемит ожидания транзакции

        // `status = 9` - ошибка проведения заказа

        // `status = 10` - максимальное время соединения превышено, при этом задача не начала выполняться

        // Добавляем тестовый заказ
        $response = $client->addOrder(new RequestAddOrder(self::$clientUUIDExists));
        $this->assertEquals(ResponseAddOrder::STATUS_SUCCESS, $response->status, "cannot add test order");
        $orderUUID = $response->orderUUID;

        // `status = 11` - заказ уже удален
        $request = (new RequestAddGoodsToOrder($orderUUID))
            ->addGood(new GoodItem(self::$goodUUIDMany, 1));
        $response = $client->addGoodsToOrder($request);
        $this->assertEquals(ResponseAddGoodsToOrder::STATUS_SUCCESS, $response->status);
        $this->assertCount(1, $response->goods);
        $this->assertCount(1, $response->order);
        $client->resetOrder(new RequestResetOrder($orderUUID));
        $request = (new RequestAddGoodsToOrder($orderUUID))
            ->addGood(new GoodItem(self::$goodUUIDMany, 0));
        $response = $client->addGoodsToOrder($request);
        $this->assertEquals(ResponseAddGoodsToOrder::STATUS_ORDER_RESETED, $response->status);

        // Добавляем тестовый заказ
        $response = $client->addOrder(new RequestAddOrder(self::$clientUUIDExists));
        $this->assertEquals(ResponseAddOrder::STATUS_SUCCESS, $response->status, "cannot add test order");
        $orderUUID = $response->orderUUID;

        // `status = 0` - все хорошо
        $request = (new RequestAddGoodsToOrder($orderUUID))
            ->addGood(new GoodItem(self::$goodUUIDMoreThen_0_1, 1))
            ->addGood(new GoodItem(self::$goodUUIDMoreThen_0_2, 1));
        $response = $client->addGoodsToOrder($request);
        $this->assertEquals(ResponseAddGoodsToOrder::STATUS_SUCCESS, $response->status);
        $this->assertCount(2, $response->order);
        $this->assertCount(2, $response->goods);
        $request = (new RequestAddGoodsToOrder($orderUUID))
            ->addGood(new GoodItem(self::$goodUUIDMoreThen_0_3, 1))
            ->addGood(new GoodItem(self::$goodUUIDMoreThen_0_4, 1));
        $response = $client->addGoodsToOrder($request);
        $this->assertEquals(ResponseAddGoodsToOrder::STATUS_SUCCESS, $response->status);
        $this->assertCount(4, $response->order);
        $this->assertCount(2, $response->goods);

        // Сотрем тестовый заказ
        $response = $client->resetOrder(new RequestResetOrder($orderUUID));
        $this->assertEquals(ResponseResetOrder::STATUS_SUCCESS, $response->status, "cannot reset test order");

        // 2. Проверка частных статусов по товарам

        // Добавляем тестовый заказ
        $response = $client->addOrder(new RequestAddOrder(self::$clientUUIDExists));
        $this->assertEquals(ResponseAddOrder::STATUS_SUCCESS, $response->status, "cannot add test order");
        $orderUUID = $response->orderUUID;

        // `goods[n].status = 1` - `goods[n].goodUUID` не передан
        $goodItem = new GoodItem(self::$validUUID, 1);
        $goodItem->goodUUID = '';
        $request = (new RequestAddGoodsToOrder($orderUUID))
            ->addGood($goodItem);
        $response = $client->addGoodsToOrder($request);
        $this->assertEquals(ResponseAddGoodsToOrder::STATUS_EXECUTED_NOT_ENOUGH, $response->status);
        $this->assertCount(1, $response->goods);
        $this->assertEmpty($response->order);
        $this->assertInstanceOf(GoodState::class, $response->goods[0]);
        $this->assertEquals(GoodState::STATUS_EMPTY_GOOD_UUID, $response->goods[0]->status);

        // `goods[n].status = 2` - `goods[n].goodUUID` не корректный
        $goodItem = new GoodItem(self::$validUUID, 1);
        $goodItem->goodUUID = self::$invalidUUID;
        $request = (new RequestAddGoodsToOrder($orderUUID))
            ->addGood($goodItem);
        $response = $client->addGoodsToOrder($request);
        $this->assertEquals(ResponseAddGoodsToOrder::STATUS_EXECUTED_NOT_ENOUGH, $response->status);
        $this->assertCount(1, $response->goods);
        $this->assertEmpty($response->order);
        $this->assertInstanceOf(GoodState::class, $response->goods[0]);
        $this->assertEquals(GoodState::STATUS_INVALID_GOOD_UUID, $response->goods[0]->status);

        // `goods[n].status = 3` - товар с идентификатором `goods[n].goodUUID` не найден
        $request = (new RequestAddGoodsToOrder($orderUUID))
            ->addGood(new GoodItem(self::$goodUUIDNotFound, 1));
        $response = $client->addGoodsToOrder($request);
        $this->assertEquals(ResponseAddGoodsToOrder::STATUS_EXECUTED_NOT_ENOUGH, $response->status);
        $this->assertCount(1, $response->goods);
        $this->assertEmpty($response->order);
        $this->assertInstanceOf(GoodState::class, $response->goods[0]);
        $this->assertEquals(GoodState::STATUS_GOOD_NOT_FOUND, $response->goods[0]->status);

        // `goods[n].status = 4` - `goods[n].count` не был передан
        // Нельзя проверить

        // `goods[n].status = 5` - количество `goods[n].count` не корректно (отрицательное)
        $goodItem = new GoodItem(self::$goodUUIDMany, 1);
        $goodItem->count = -1;
        $request = (new RequestAddGoodsToOrder($orderUUID))
            ->addGood($goodItem);
        $response = $client->addGoodsToOrder($request);
        $this->assertEquals(ResponseAddGoodsToOrder::STATUS_EXECUTED_NOT_ENOUGH, $response->status);
        $this->assertCount(1, $response->goods);
        $this->assertEmpty($response->order);
        $this->assertInstanceOf(GoodState::class, $response->goods[0]);
        $this->assertEquals(GoodState::STATUS_INVALID_COUNT, $response->goods[0]->status);

        // `goods[n].status = 6` - товар добавлен в корзину частично (меньше, чем требовалось `< goods[n].count`)
        $request = (new RequestAddGoodsToOrder($orderUUID))
            ->addGood(new GoodItem(self::$goodUUIDMoreThen_0_lessThen_10, 15));
        $response = $client->addGoodsToOrder($request);
        $this->assertEquals(ResponseAddGoodsToOrder::STATUS_EXECUTED_NOT_ENOUGH, $response->status);
        $this->assertCount(1, $response->goods);
        $this->assertInstanceOf(GoodState::class, $response->goods[0]);
        $this->assertEquals(GoodState::STATUS_ADDED_NOT_ENOUGH, $response->goods[0]->status);

        // `goods[n].status = 7` - товар не может быть добавлен из-за нулевого остатка
        $request = (new RequestAddGoodsToOrder($orderUUID))
            ->addGood(new GoodItem(self::$goodUUIDHaveNot, 2));
        $response = $client->addGoodsToOrder($request);
        $this->assertEquals(ResponseAddGoodsToOrder::STATUS_EXECUTED_NOT_ENOUGH, $response->status);
        $this->assertCount(1, $response->goods);
        $this->assertInstanceOf(GoodState::class, $response->goods[0]);
        $this->assertEquals(GoodState::STATUS_GOOD_HAVE_NOT, $response->goods[0]->status);

        // `goods[n].status = 8` - товар для удаления не найден
        $request = (new RequestAddGoodsToOrder($orderUUID))
            ->addGood(new GoodItem(self::$goodUUIDExists, 0));
        $response = $client->addGoodsToOrder($request);
        $this->assertEquals(ResponseAddGoodsToOrder::STATUS_EXECUTED_NOT_ENOUGH, $response->status);
        $this->assertCount(1, $response->goods);
        $this->assertInstanceOf(GoodState::class, $response->goods[0]);
        $this->assertEquals(GoodState::STATUS_GOOD_TO_REMOVE_NOT_FOUND_IN_ORDER, $response->goods[0]->status);

        // `goods[n].status = 9` - запрос на изменение устарел и не будет выполнен
        // (по этой номенклатуре и заказу поступил запрос с более поздней датой)
        // Невозможно проверить

        // `goods[n].status = 0` - все хорошо
        $request = (new RequestAddGoodsToOrder($orderUUID))
            ->addGood(new GoodItem(self::$goodUUIDMany, 1));
        $response = $client->addGoodsToOrder($request);
        $this->assertEquals(ResponseAddGoodsToOrder::STATUS_SUCCESS, $response->status);
        $this->assertCount(1, $response->goods);
        $this->assertInstanceOf(GoodState::class, $response->goods[0]);
        $this->assertEquals(GoodState::STATUS_SUCCESS, $response->goods[0]->status);

        // Сотрем тестовый заказ
        $response = $client->resetOrder(new RequestResetOrder($orderUUID));
        $this->assertEquals(ResponseResetOrder::STATUS_SUCCESS, $response->status, "cannot reset test order");
    }

    /**
     * @large
     */
    final public function testBooking()
    {
        $client = $this->getClient();

        // Добавляем тестовый заказ
        $response = $client->addOrder(new RequestAddOrder(self::$clientUUIDExists));
        $this->assertEquals(ResponseAddOrder::STATUS_SUCCESS, $response->status, "cannot add test order");
        $orderUUID = $response->orderUUID;

        // Добавили 1 товар
        $response = $client->addGoodToOrder(new RequestAddGoodToOrder($orderUUID, self::$goodUUIDMoreThen_0_1, 1));
        $this->assertEquals(ResponseAddGoodToOrder::STATUS_SUCCESS, $response->status);
        $this->assertEquals(1, $response->count);
        $this->assertEquals(1, $response->required);
        $this->assertCount(1, $response->order);

        // Добавили 1 товар
        $response = $client->addGoodToOrder(new RequestAddGoodToOrder($orderUUID, self::$goodUUIDMoreThen_0_2, 1));
        $this->assertEquals(ResponseAddGoodToOrder::STATUS_SUCCESS, $response->status);
        $this->assertEquals(1, $response->count);
        $this->assertEquals(1, $response->required);
        $this->assertCount(2, $response->order);

        // Добавтли 2 товара
        $request = (new RequestAddGoodsToOrder($orderUUID))
            ->addGood(new GoodItem(self::$goodUUIDMoreThen_0_3, 1))
            ->addGood(new GoodItem(self::$goodUUIDMoreThen_0_4, 1));
        $response = $client->addGoodsToOrder($request);
        $this->assertEquals(ResponseAddGoodsToOrder::STATUS_SUCCESS, $response->status);
        $this->assertCount(2, $response->goods);
        $this->assertCount(4, $response->order);

        // Добавили 1 товар
        $response = $client->addGoodToOrder(new RequestAddGoodToOrder($orderUUID, self::$goodUUIDMany, 1));
        $this->assertEquals(ResponseAddGoodToOrder::STATUS_SUCCESS, $response->status);
        $this->assertEquals(1, $response->count);
        $this->assertEquals(1, $response->required);
        $this->assertCount(5, $response->order);

        // Удалим 2 товара
        $request = (new RequestAddGoodsToOrder($orderUUID))
            ->addGood(new GoodItem(self::$goodUUIDMoreThen_0_3, 0))
            ->addGood(new GoodItem(self::$goodUUIDMoreThen_0_4, 0));
        $response = $client->addGoodsToOrder($request);
        $this->assertEquals(ResponseAddGoodsToOrder::STATUS_SUCCESS, $response->status);
        $this->assertCount(2, $response->goods);
        $this->assertCount(3, $response->order);

        // Сотрем тестовый заказ
        $response = $client->resetOrder(new RequestResetOrder($orderUUID));
        $this->assertEquals(ResponseResetOrder::STATUS_SUCCESS, $response->status, "cannot reset test order");
    }

    /**
     * @large
     */
    final public function testAddOneAsList()
    {
        $client = $this->getClient();

        $response = $client->addOrder(new RequestAddOrder(self::$clientUUIDExists));
        $this->assertEquals(ResponseAddOrder::STATUS_SUCCESS, $response->status, "cannot add test order");
        $orderUUID = $response->orderUUID;

        $request = new RequestAddGoodsToOrder($orderUUID, [
            new GoodItem(self::$goodUUIDMany, 1),
        ]);
        $response = $client->addGoodsToOrder($request);
        $this->assertEquals(ResponseAddGoodsToOrder::STATUS_SUCCESS, $response->status);
        $this->assertCount(1, $response->goods);
        $this->assertCount(1, $response->order);

        // Сотрем тестовый заказ
        $response = $client->resetOrder(new RequestResetOrder($orderUUID));
        $this->assertEquals(ResponseResetOrder::STATUS_SUCCESS, $response->status, "cannot reset test order");
    }
}
