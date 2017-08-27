<?php

namespace Example\Client1C\ResponseTypes;

/**
 * Class GoodState
 *
 * @package Example\Client1C\ResponseTypes
 */
class GoodState
{
    const STATUS_SUCCESS                           = 0; // все хорошо
    const STATUS_EMPTY_GOOD_UUID                   = 1; // `goods[n].goodUUID` не передан
    const STATUS_INVALID_GOOD_UUID                 = 2; // `goods[n].goodUUID` не корректный
    const STATUS_GOOD_NOT_FOUND                    = 3; // товар `goods[n].goodUUID` не найден
    const STATUS_EMPTY_COUNT                       = 4; // `goods[n].count` не был передан
    const STATUS_INVALID_COUNT                     = 5; // количество `goods[n].count` не корректно
    const STATUS_ADDED_NOT_ENOUGH                  = 6; // товар добавлен в корзину частично
    const STATUS_GOOD_HAVE_NOT                     = 7; // товар не может быть добавлен из-за нулевого остатка
    const STATUS_GOOD_TO_REMOVE_NOT_FOUND_IN_ORDER = 8; // товар для удаления не найден
    const STATUS_REQUEST_IS_OUT_OF_DATE            = 9; // запрос на изменение устарел и не будет выполнен

    /**
     * @var string $goodUUID Good's UUID
     * @var int    $count    How many goods actually was created
     * @var int    $required How many goods was required
     * @var int    $status   Status code
     */
    public $goodUUID, $count = -1, $required = -1, $status = -1;

    /**
     * GoodState constructor
     *
     * @param int         $status
     * @param string|null $goodUUID
     * @param int         $count
     * @param int         $required
     */
    public function __construct($status = -1, $goodUUID = null, $count = -1, $required = -1)
    {
        $this->goodUUID = $goodUUID;
        $this->count = $count;
        $this->required = $required;
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return print_r($this, true);
    }
}
