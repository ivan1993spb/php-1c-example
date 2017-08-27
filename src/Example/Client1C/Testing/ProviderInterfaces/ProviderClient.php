<?php

namespace Example\Client1C\Testing\ProviderInterfaces;

/**
 * Interface ProviderClient
 *
 * @package Example\Client1C\Testing\ProviderInterfaces
 */
interface ProviderClient
{
    /**
     * @return bool
     */
    public function hasAgreement();

    /**
     * @return string
     */
    public function UUID();

    /**
     * @return string
     */
    public function INN();

    /**
     * @return int
     */
    public function VAT();

    /**
     * @return int
     */
    public function payType();

    /**
     * @return int
     */
    public function deliveryType();

    /**
     * @return float
     */
    public function discountGenFlowPercent();

    /**
     * @return float
     */
    public function discountGenProfitPercent();

    /**
     * @return string
     */
    public function discountFinishDate();

    /**
     * @return float
     */
    public function discountPrevSum();

    /**
     * @return float
     */
    public function discountNextSum();

    /**
     * @return float
     */
    public function currentSum();
}
