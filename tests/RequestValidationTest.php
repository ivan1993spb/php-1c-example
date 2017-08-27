<?php

use Example\Client1C\RequestTypes\GoodItem;
use Example\Client1C\RequestTypes\RequestAddGoodsToOrder;
use Example\Client1C\RequestTypes\RequestAddGoodToOrder;
use Example\Client1C\RequestTypes\RequestAddOrder;
use Example\Client1C\RequestTypes\RequestConfirmOrder;
use Example\Client1C\RequestTypes\RequestResetOrder;
use PHPUnit\Framework\TestCase;

class RequestValidationTest extends TestCase
{
    private static $validUUID = '00000000-0000-0000-0000-000000000000';
    private static $invalidUUID = 'invalid uuid';

    /**
     * @small
     * @expectedException \Example\Client1C\Exceptions\InvalidRequestParamException
     */
    public function testGoodItemEmptyGoodUUID()
    {
        new GoodItem('', 0);
    }

    /**
     * @small
     * @expectedException \Example\Client1C\Exceptions\InvalidRequestParamException
     */
    public function testGoodItemInvalidGoodUUID()
    {
        new GoodItem(self::$invalidUUID, 0);
    }

    /**
     * @small
     * @expectedException \Example\Client1C\Exceptions\InvalidRequestParamException
     */
    public function testGoodItemInvalidCount()
    {
        new GoodItem(self::$validUUID, -1);
    }

    /**
     * @small
     */
    public function testGoodItem()
    {
        new GoodItem(self::$validUUID, 1);
    }

    /**
     * @small
     * @expectedException \Example\Client1C\Exceptions\InvalidRequestParamException
     */
    public function testRequestAddGoodsToOrderEmptyOrderUUID()
    {
        new RequestAddGoodsToOrder('');
    }

    /**
     * @small
     * @expectedException \Example\Client1C\Exceptions\InvalidRequestParamException
     */
    public function testRequestAddGoodsToOrderInvalidOrderUUID()
    {
        new RequestAddGoodsToOrder(self::$invalidUUID);
    }

    /**
     * @small
     */
    public function testRequestAddGoodsToOrder()
    {
        new RequestAddGoodsToOrder(self::$validUUID);
    }

    /**
     * @small
     * @expectedException \Example\Client1C\Exceptions\InvalidRequestParamException
     */
    public function testRequestAddGoodToOrderEmptyOrderUUID()
    {
        new RequestAddGoodToOrder('', '', 0);
    }

    /**
     * @small
     * @expectedException \Example\Client1C\Exceptions\InvalidRequestParamException
     */
    public function testRequestAddGoodToOrderInvalidOrderUUID()
    {
        new RequestAddGoodToOrder(self::$invalidUUID, '', 0);
    }

    /**
     * @small
     * @expectedException \Example\Client1C\Exceptions\InvalidRequestParamException
     */
    public function testRequestAddGoodToOrderEmptyGoodUUID()
    {
        new RequestAddGoodToOrder(self::$validUUID, '', 0);
    }

    /**
     * @small
     * @expectedException \Example\Client1C\Exceptions\InvalidRequestParamException
     */
    public function testRequestAddGoodToOrderInvalidGoodUUID()
    {
        new RequestAddGoodToOrder(self::$validUUID, self::$invalidUUID, 0);
    }

    /**
     * @small
     * @expectedException \Example\Client1C\Exceptions\InvalidRequestParamException
     */
    public function testRequestAddGoodToOrderInvalidCount()
    {
        new RequestAddGoodToOrder(self::$validUUID, self::$validUUID, -1);
    }

    /**
     * @small
     */
    public function testRequestAddGoodToOrder()
    {
        new RequestAddGoodToOrder(self::$validUUID, self::$validUUID, 1);
    }

    /**
     * @small
     * @expectedException \Example\Client1C\Exceptions\InvalidRequestParamException
     */
    public function testRequestAddOrderEmptyClientUUID()
    {
        new RequestAddOrder('');
    }

    /**
     * @small
     * @expectedException \Example\Client1C\Exceptions\InvalidRequestParamException
     */
    public function testRequestAddOrderInvalidClientUUID()
    {
        new RequestAddOrder(self::$invalidUUID);
    }

    /**
     * @small
     */
    public function testRequestAddOrder()
    {
        new RequestAddOrder(self::$validUUID);
    }

    /**
     * @small
     * @expectedException \Example\Client1C\Exceptions\InvalidRequestParamException
     */
    public function testRequestConfirmOrderEmptyOrderUUID()
    {
        new RequestConfirmOrder('');
    }

    /**
     * @small
     * @expectedException \Example\Client1C\Exceptions\InvalidRequestParamException
     */
    public function testRequestConfirmOrderInvalidOrderUUID()
    {
        new RequestConfirmOrder(self::$invalidUUID);
    }

    /**
     * @small
     */
    public function testRequestConfirmOrder()
    {
        new RequestConfirmOrder(self::$validUUID);
    }

    /**
     * @small
     * @expectedException \Example\Client1C\Exceptions\InvalidRequestParamException
     */
    public function testRequestResetOrderEmptyOrderUUID()
    {
        new RequestResetOrder('');
    }

    /**
     * @small
     * @expectedException \Example\Client1C\Exceptions\InvalidRequestParamException
     */
    public function testRequestResetOrderInvalidOrderUUID()
    {
        new RequestResetOrder(self::$invalidUUID);
    }

    /**
     * @small
     */
    public function testRequestResetOrder()
    {
        new RequestResetOrder(self::$validUUID);
    }
}
