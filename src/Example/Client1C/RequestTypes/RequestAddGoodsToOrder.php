<?php

namespace Example\Client1C\RequestTypes;

use Example\Client1C\Exceptions\InvalidRequestParamException;
use Example\Client1C\Request;
use Ramsey\Uuid\Uuid;
use SoapVar;

/**
 * Class RequestAddGoodsToOrder
 *
 * @package Example\Client1C\RequestTypes
 */
class RequestAddGoodsToOrder extends Request
{
    /**
     * @var string  $orderUUID UUID заказа в 1С
     * @var SoapVar $goods     Подготовленный для отправки SoapVar массив со списком товаров
     */
    public $orderUUID, $goods;

    /**
     * Здесь хранятся объекты типа GoodItem
     *
     * @var GoodItem[]
     */
    private $goodItems = [];

    /**
     * RequestAddGoodsToOrder constructor
     *
     * @param string     $orderUUID Order's good
     * @param GoodItem[] $goods
     * @throws \Example\Client1C\Exceptions\InvalidRequestParamException
     */
    public function __construct($orderUUID, array $goods = [])
    {
        if (empty($orderUUID)) {
            throw new InvalidRequestParamException("empty order UUID");
        }
        if (!Uuid::isValid($orderUUID)) {
            throw new InvalidRequestParamException("invalid order UUID");
        }

        $this->orderUUID = $orderUUID;

        if (!empty($goods)) {
            foreach ($goods as $good) {
                if ($good instanceof GoodItem) {
                    // Just update count if good items list already contains the good item
                    foreach ($this->goodItems as &$goodItem) {
                        if ($goodItem->goodUUID == $good->goodUUID) {
                            if ($goodItem->count != $good->count) {
                                $goodItem->count = $good->count;
                            }

                            continue 2;
                        }
                    }

                    // Else push new item
                    array_push($this->goodItems, $good);
                }
            }

        }

        $this->prepareGoods();
    }

    /**
     * @param GoodItem $good
     * @return RequestAddGoodsToOrder
     */
    public function addGood(GoodItem $good)
    {
        foreach ($this->goodItems as &$goodItem) {
            if ($goodItem->goodUUID == $good->goodUUID) {
                $goodItem->count = $good->count;

                // Обновить поле со списком позиций заказа
                $this->prepareGoods();

                return $this;
            }
        }

        array_push($this->goodItems, $good);

        // Обновить поле со списком позиций заказа
        $this->prepareGoods();

        return $this;
    }

    /**
     * @return \Generator|GoodItem[]
     */
    public function getGoodItems()
    {
        foreach ($this->goodItems as $goodItem) {
            yield $goodItem;
        }
    }

    /**
     * @return bool
     */
    public function empty()
    {
        return empty($this->goodItems);
    }


    public function __sleep()
    {
        // $this->goods не сериализуем, потому что $this->goods - подготовленная для отправки копия $this->goodItems
        return ['orderUUID', 'goodItems'];
    }

    public function __wakeup()
    {
        // Обновить поле со списком позиций заказа
        $this->prepareGoods();
    }

    /**
     * Подготавливает список позиций заказа для отправки в 1С
     */
    private function prepareGoods()
    {
        /** @var SoapVar[] $soapGoods */
        $soapGoods = [];

        foreach ($this->goodItems as $goodItem) {
            array_push($soapGoods, new SoapVar($goodItem, SOAP_ENC_OBJECT));
        }

        $this->goods = new SoapVar($soapGoods, SOAP_ENC_ARRAY);
    }

    /**
     * @return array
     */
    public function __debugInfo()
    {
        return [
            'orderUUID' => $this->orderUUID,
            'goodItems' => $this->goodItems,
        ];
    }
}
