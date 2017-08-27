<?php

namespace Example\Client1C\Testing\MemoryProviders;

use Example\Client1C\Testing\Exceptions\GoodNotFoundException;
use Example\Client1C\Testing\Exceptions\InvalidUUIDException;
use Example\Client1C\Testing\ProviderInterfaces\ProviderGood as ProviderGoodInterface;
use Example\Client1C\Testing\ProviderInterfaces\ProviderGoods as ProviderGoodsInterface;
use Ramsey\Uuid\Uuid;

/**
 * Class ProviderGoods
 *
 * @package Example\Client1C\Testing\MemoryProviders
 */
class ProviderGoods implements ProviderGoodsInterface
{
    /**
     * @var \Example\Client1C\Testing\ProviderInterfaces\ProviderGood[]
     */
    private $goods = [];

    /**
     * @var bool
     */
    private $goodAlwaysExists = false;

    /**
     * @var ProviderGoodFactory|null
     */
    private $providerGoodFactory = null;

    /**
     * ProviderGoods constructor
     *
     * @param bool                     $goodAlwaysExists
     * @param ProviderGoodFactory|null $providerGoodFactory
     */
    public function __construct($goodAlwaysExists = false, ProviderGoodFactory $providerGoodFactory = null)
    {
        $this->goodAlwaysExists = $goodAlwaysExists;
        $this->providerGoodFactory = $providerGoodFactory;
    }

    /**
     * @param ProviderGoodInterface $good
     * @return ProviderGoods
     */
    public function addGood(ProviderGoodInterface $good)
    {
        foreach ($this->goods as $goodExists) {
            if ($good->UUID() == $goodExists->UUID()) {
                // Return if good with the same UUID already exists
                return $this;
            }
        }

        array_push($this->goods, $good);
        return $this;
    }

    /**
     * @param string $goodUUID
     * @return \Example\Client1C\Testing\ProviderInterfaces\ProviderGood
     * @throws \Example\Client1C\Testing\Exceptions\InvalidUUIDException
     * @throws \Example\Client1C\Testing\Exceptions\GoodNotFoundException
     */
    public function getGood($goodUUID)
    {
        if (!Uuid::isValid($goodUUID)) {
            throw new InvalidUUIDException();
        }

        foreach ($this->goods as $good) {
            if ($good->UUID() === $goodUUID) {
                return $good;
            }
        }

        // В тестовом режиме может пригодиться такое поведение провайдера товаров, когда провайдер
        // делает вид, что товар всегда существет. Если товар не существует, то провайдер добавляет
        // товар самостоятельно
        if ($this->goodAlwaysExists) {
            /** @var ProviderGood $good */
            $good = null;

            if (empty($this->providerGoodFactory)) {
                $good = new ProviderGood($goodUUID);
            } else {
                $good = $this->providerGoodFactory->create($goodUUID);
            }

            if (!empty($good)) {
                array_push($this->goods, $good);
                return $good;
            }
        }

        throw new GoodNotFoundException();
    }
}
