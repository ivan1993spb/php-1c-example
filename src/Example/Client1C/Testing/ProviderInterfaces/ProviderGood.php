<?php

namespace Example\Client1C\Testing\ProviderInterfaces;

/**
 * Interface ProviderGood
 *
 * @package Example\Client1C\Testing\ProviderInterfaces
 */
interface ProviderGood
{
    /**
     * @return string
     */
    public function UUID();

    /**
     * @return float
     */
    public function price();

    /**
     * Количество незарезервированного товара на складе
     *
     * @return int
     */
    public function count();

    /**
     * Пытается зарезервировать переданное количество товара и возвращает число - сколько удалось зарезервировать
     *
     * @param int $count
     * @return int
     */
    public function reserve($count);

    /**
     * Разрезервирует переданное количество товара и вернет общее количество товара на складе доступное для заказа
     *
     * @param int $count
     * @return int
     */
    public function release($count);
}
