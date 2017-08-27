<?php

namespace Example\Client1C\Testing\MemoryProviders;

use Example\Client1C\Testing\ProviderInterfaces\ProviderClient as ProviderClientInterface;
use Ramsey\Uuid\Uuid;

/**
 * Class ProviderClient
 *
 * @package Example\Client1C\Testing\MemoryProviders
 */
class ProviderClient implements ProviderClientInterface
{
    /**
     * @var string      $UUID
     * @var bool        $hasAgreement
     * @var string|null $INN
     */
    private $UUID, $hasAgreement = true, $INN = null;

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
     * ProviderClient constructor
     *
     * @param string|null $clientUUID
     * @param bool        $hasAgreement
     * @param int         $VAT
     * @param int         $payType
     * @param int         $deliveryType
     * @param float       $discountGenFlowPercent
     * @param float       $discountGenProfitPercent
     * @param string|null $discountFinishDate Date in format dd.mm.yyyy
     * @param float       $discountPrevSum
     * @param float       $discountNextSum
     * @param float       $currentSum
     */
    public function __construct($clientUUID = null, $INN = null, $hasAgreement = true, $VAT = -1, $payType = -1, $deliveryType = -1,
        $discountGenFlowPercent = -1.0, $discountGenProfitPercent = -1.0, $discountFinishDate = null, $discountPrevSum = -1.0,
        $discountNextSum = -1.0, $currentSum = -1.0)
    {
        if ($clientUUID === null) {
            $clientUUID = Uuid::getFactory()->uuid4()->toString();
        }

        $this->UUID = $clientUUID;
        $this->INN = $INN;
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
     * @return bool
     */
    public function hasAgreement()
    {
        return $this->hasAgreement;
    }

    /**
     * @return string
     */
    public function UUID()
    {
        return $this->UUID;
    }

    /**
     * @return int
     */
    public function VAT()
    {
        return $this->VAT;
    }

    /**
     * @return int
     */
    public function payType()
    {
        return $this->payType;
    }

    /**
     * @return int
     */
    public function deliveryType()
    {
        return $this->deliveryType;
    }

    /**
     * @return float
     */
    public function discountGenFlowPercent()
    {
        return $this->discountGenFlowPercent;
    }

    /**
     * @return float
     */
    public function discountGenProfitPercent()
    {
        return $this->discountGenProfitPercent;
    }

    /**
     * @return string
     */
    public function discountFinishDate()
    {
        return $this->discountFinishDate;
    }

    /**
     * @return float
     */
    public function discountPrevSum()
    {
        return $this->discountPrevSum;
    }

    /**
     * @return float
     */
    public function discountNextSum()
    {
        return $this->discountNextSum;
    }

    /**
     * @return float
     */
    public function currentSum()
    {
        return $this->currentSum;
    }

    /**
     * @return string
     */
    public function INN()
    {
        return $this->INN;
    }
}
