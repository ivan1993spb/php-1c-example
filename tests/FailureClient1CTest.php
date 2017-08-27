<?php

use Example\Client1C\RequestTypes\RequestAddGoodsToOrder;
use Example\Client1C\RequestTypes\RequestAddGoodToOrder;
use Example\Client1C\RequestTypes\RequestAddOrder;
use Example\Client1C\RequestTypes\RequestConfirmOrder;
use Example\Client1C\RequestTypes\RequestResetOrder;
use Example\Client1C\Testing\FailureClient1C;
use PHPUnit\Framework\TestCase;

/**
 * Class Client1CWrapperTest
 */
class FailureClient1CTest extends TestCase
{
    private static $validUUID = '00000000-0000-0000-0000-000000000000';

    /**
     * @small
     * @expectedException Example\Client1C\Exceptions\MethodInvocationException
     */
    public function testMethodAddOrder()
    {
        $client = new FailureClient1C;
        $client->addOrder(new RequestAddOrder(self::$validUUID));
    }

    /**
     * @small
     * @expectedException Example\Client1C\Exceptions\MethodInvocationException
     */
    public function testMethodAddGoodToOrder()
    {
        $client = new FailureClient1C;
        $client->addGoodToOrder(new RequestAddGoodToOrder(self::$validUUID, self::$validUUID, 0));
    }

    /**
     * @small
     * @expectedException Example\Client1C\Exceptions\MethodInvocationException
     */
    public function testMethodAddGoodsToOrder()
    {
        $client = new FailureClient1C;
        $client->addGoodsToOrder(new RequestAddGoodsToOrder(self::$validUUID));
    }

    /**
     * @small
     * @expectedException Example\Client1C\Exceptions\MethodInvocationException
     */
    public function testMethodConfirmOrder()
    {
        $client = new FailureClient1C;
        $client->confirmOrder(new RequestConfirmOrder(self::$validUUID));
    }

    /**
     * @small
     * @expectedException Example\Client1C\Exceptions\MethodInvocationException
     */
    public function testMethodResetOrder()
    {
        $client = new FailureClient1C;
        $client->resetOrder(new RequestResetOrder(self::$validUUID));
    }
}
