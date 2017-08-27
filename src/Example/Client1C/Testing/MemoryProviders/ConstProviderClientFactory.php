<?php

namespace Example\Client1C\Testing\MemoryProviders;

/**
 * Class ConstProviderClientFactory
 *
 * @package Example\Client1C\Testing\MemoryProviders
 */
class ConstProviderClientFactory implements ProviderClientFactory
{
    // Default values
    const DEFAULT_VAT           = 18; // %
    const DEFAULT_PAY_TYPE      = 0;
    const DEFAULT_DELIVERY_TYPE = 0;

    /**
     * @var bool $hasAgreement
     */
    private $hasAgreement = true;

    /**
     * @var int $VAT
     * @var int $payType
     * @var int $deliveryType
     */
    private $VAT = -1, $payType = -1, $deliveryType = -1;

    /**
     * @var float  $discountGenFlowPercent
     * @var float  $discountGenProfitPercent
     * @var string $discountFinishDate
     * @var float  $discountPrevSum
     * @var float  $discountNextSum
     * @var float  $currentSum
     */
    private $discountGenFlowPercent = -1.0, $discountGenProfitPercent = -1.0, $discountFinishDate, $discountPrevSum = -1.0,
        $discountNextSum = -1.0, $currentSum = -1.0;

    /**
     * ConstProviderClientFactory constructor
     *
     * @param bool  $hasAgreement
     * @param int   $VAT
     * @param int   $payType
     * @param int   $deliveryType
     * @param float $discountGenFlowPercent
     * @param float $discountGenProfitPercent
     * @param null  $discountFinishDate
     * @param float $discountPrevSum
     * @param float $discountNextSum
     * @param float $currentSum
     */
    public function __construct($hasAgreement = true, $VAT = self::DEFAULT_VAT, $payType = self::DEFAULT_PAY_TYPE,
        $deliveryType = self::DEFAULT_DELIVERY_TYPE, $discountGenFlowPercent = -1.0, $discountGenProfitPercent = -1.0,
        $discountFinishDate = null, $discountPrevSum = -1.0, $discountNextSum = -1.0, $currentSum = -1.0)
    {
        $this->hasAgreement = $hasAgreement;
        $this->VAT = $VAT;
        $this->payType = $payType;
        $this->deliveryType = $deliveryType;

        // Discount data
        $this->discountGenFlowPercent = $discountGenFlowPercent;
        $this->discountGenProfitPercent = $discountGenProfitPercent;

        if ($discountFinishDate === null) {
            $discountFinishDate = '00.00.0000';
        }

        $this->discountFinishDate = $discountFinishDate;

        $this->discountPrevSum = $discountPrevSum;
        $this->discountNextSum = $discountNextSum;
        $this->currentSum = $currentSum;
    }

    /**
     * @param string|null $clientUUID
     * @param string|null $INN
     * @return ProviderClient
     */
    public function create($clientUUID = null, $INN = null): ProviderClient
    {
        return new ProviderClient($clientUUID, $INN, $this->hasAgreement, $this->VAT, $this->payType, $this->deliveryType,
            $this->discountGenFlowPercent, $this->discountGenProfitPercent, $this->discountFinishDate, $this->discountPrevSum,
            $this->discountNextSum, $this->currentSum);
    }
}
