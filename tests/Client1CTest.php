<?php

use Example\Client1C\Client1C;

/**
 * Class Client1CTest
 */
class Client1CTest extends Client1CTestBase
{
    /**
     * Конфигурационный файл для теста веб-сервисов
     *
     * @var string
     */
    const TEST_CONFIG_FILE = __DIR__.'/../phpunit_client_1c_config.php';

    /**
     * Default test configuration
     *
     * @var array
     */
    private static $defaultConf = [

        // Корректный UUID
        'valid-uuid' => '00000000-0000-0000-0000-000000000000',

        // UUID несуществующего клиента
        'client-uuid-not-found' => '00000000-0000-0000-0001-000000000000',

        // UUID существующего клиента
        'client-uuid-exists' => '',

        // Некорректный UUID
        'invalid-uuid' => 'invalid uuid',

        // UUID несуществующего заказа
        'order-uuid-not-found' => '00000000-0000-0000-0002-000000000000',

        // UUID несуществующей номенклатуры
        'good-uuid-not-found' => '00000000-0000-0000-0003-000000000000',

        // UUID товара, которого очень много на складе
        'good-uuid-many' => '',

        // UUID товара, которого > 0 и < 10 штук на складе
        'good-uuid-more-then-0-less-then-10' => '',

        // UUID товара, которого 0 штук на складе
        'good-uuid-have-not' => '',

        // UUID существующего товара
        'good-uuid-exists' => '',

        // UUID товаров, которых > 0 на складе
        'good-uuid-more-then-0-1' => '',
        'good-uuid-more-then-0-2' => '',
        'good-uuid-more-then-0-3' => '',
        'good-uuid-more-then-0-4' => '',
    ];

    /**
     * @var bool
     */
    static private $skipTests = true;

    public static function setUpBeforeClass()
    {
        if (file_exists(self::TEST_CONFIG_FILE)) {
            $config = require self::TEST_CONFIG_FILE;
            if (is_array($config)) {
                $config = array_replace(self::$defaultConf, $config);

                self::$validUUID                      = $config['valid-uuid'];
                self::$clientUUIDNotFound             = $config['client-uuid-not-found'];
                self::$clientUUIDHasNotAgreement      = $config['client-uuid-has-not-agreement'];
                self::$clientUUIDExists               = $config['client-uuid-exists'];
                self::$invalidUUID                    = $config['invalid-uuid'];
                self::$orderUUIDNotFound              = $config['order-uuid-not-found'];
                self::$goodUUIDNotFound               = $config['good-uuid-not-found'];
                self::$goodUUIDMany                   = $config['good-uuid-many'];
                self::$goodUUIDMoreThen_0_lessThen_10 = $config['good-uuid-more-then-0-less-then-10'];
                self::$goodUUIDHaveNot                = $config['good-uuid-have-not'];
                self::$goodUUIDExists                 = $config['good-uuid-exists'];
                self::$goodUUIDMoreThen_0_1           = $config['good-uuid-more-then-0-1'];
                self::$goodUUIDMoreThen_0_2           = $config['good-uuid-more-then-0-2'];
                self::$goodUUIDMoreThen_0_3           = $config['good-uuid-more-then-0-3'];
                self::$goodUUIDMoreThen_0_4           = $config['good-uuid-more-then-0-4'];

                self::$skipTests = false;
            } else {
                fprintf(STDERR, "testing config file is invalid\n");
            }
        } else {
            fprintf(STDERR, "testing config file not found\n");
        }
    }

    public function setUp()
    {
        if (self::$skipTests) {
            $this->markTestSkipped('tests of 1C client are not configured');
        }
    }

    /**
     * @return \Example\Client1C\Client1CInterface
     */
    protected function getClient()
    {
        return new Client1C("");
    }
}
