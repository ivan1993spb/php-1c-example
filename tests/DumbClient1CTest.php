<?php

use Example\Client1C\Testing\DumbClient1C;
use Example\Client1C\Testing\MemoryProviders\ProviderClient;
use Example\Client1C\Testing\MemoryProviders\ProviderClients;
use Example\Client1C\Testing\MemoryProviders\ProviderGood;
use Example\Client1C\Testing\MemoryProviders\ProviderGoods;
use Example\Client1C\Testing\MemoryProviders\ProviderOrders;
use Ramsey\Uuid\Uuid;

/**
 * Class DumbClientTest
 */
class DumbClient1CTest extends Client1CTestBase
{
    /**
     * @var Example\Client1C\Testing\DumbClient1C
     */
    private static $client;

    public static function setUpBeforeClass()
    {
        // Корректный UUID
        self::$validUUID = '00000000-0000-0000-0000-000000000000';

        // UUID несуществующего клиента
        self::$clientUUIDNotFound = Uuid::getFactory()->uuid4()->toString();

        // UUID клиента не имеющего соглашение
        self::$clientUUIDHasNotAgreement = Uuid::getFactory()->uuid4()->toString();

        // UUID существующего клиента
        self::$clientUUIDExists = Uuid::getFactory()->uuid4()->toString();

        // Некорректный UUID
        self::$invalidUUID = 'invalid uuid';

        // UUID несуществующего заказа
        self::$orderUUIDNotFound = Uuid::getFactory()->uuid4()->toString();

        // UUID несуществующей номенклатуры
        self::$goodUUIDNotFound = Uuid::getFactory()->uuid4()->toString();

        // UUID товара, которого очень много на складе
        self::$goodUUIDMany = Uuid::getFactory()->uuid4()->toString();

        // UUID товара, которого > 0 и < 10 штук на складе
        self::$goodUUIDMoreThen_0_lessThen_10 = Uuid::getFactory()->uuid4()->toString();

        // UUID товара, которого 0 штук на складе
        self::$goodUUIDHaveNot = Uuid::getFactory()->uuid4()->toString();

        // UUID существующего товара
        self::$goodUUIDExists = Uuid::getFactory()->uuid4()->toString();

        // UUID товаров, которых > 0 на складе
        self::$goodUUIDMoreThen_0_1 = Uuid::getFactory()->uuid4()->toString();
        self::$goodUUIDMoreThen_0_2 = Uuid::getFactory()->uuid4()->toString();
        self::$goodUUIDMoreThen_0_3 = Uuid::getFactory()->uuid4()->toString();
        self::$goodUUIDMoreThen_0_4 = Uuid::getFactory()->uuid4()->toString();


        // Определения провайдеров
        $providerGoods = (new ProviderGoods())
            ->addGood(new ProviderGood(self::$goodUUIDMany, 2500, 1000))
            ->addGood(new ProviderGood(self::$goodUUIDMoreThen_0_lessThen_10, 2500, 4))
            ->addGood(new ProviderGood(self::$goodUUIDHaveNot, 2500, 0))
            ->addGood(new ProviderGood(self::$goodUUIDExists, 2500, 100))
            ->addGood(new ProviderGood(self::$goodUUIDMoreThen_0_1, 2500, 100))
            ->addGood(new ProviderGood(self::$goodUUIDMoreThen_0_2, 2500, 100))
            ->addGood(new ProviderGood(self::$goodUUIDMoreThen_0_3, 2500, 100))
            ->addGood(new ProviderGood(self::$goodUUIDMoreThen_0_4, 2500, 100));

        $providerClients = (new ProviderClients())
            ->addClient(new ProviderClient(self::$clientUUIDExists))
            ->addClient(new ProviderClient(self::$clientUUIDHasNotAgreement, null, false));

        $providerOrders = new ProviderOrders();

        self::$client = new DumbClient1C($providerGoods, $providerClients, $providerOrders);
    }

    /**
     * @return \Example\Client1C\Client1CInterface
     */
    protected function getClient()
    {
        return self::$client;
    }
}
