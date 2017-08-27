<?php

namespace Example\Client1C;

use Example\Client1C\Exceptions\UnsupportedRequestException;
use Example\Client1C\RequestTypes\RequestAddGoodsToOrder;
use Example\Client1C\RequestTypes\RequestAddGoodToOrder;
use Example\Client1C\RequestTypes\RequestAddOrder;
use Example\Client1C\RequestTypes\RequestConfirmOrder;
use Example\Client1C\RequestTypes\RequestResetOrder;

/**
 * Class Client1CWrapper Обертка для Client1C с одним методом send для всех типов запросов к 1С
 *
 * @package Example\Client1C
 */
class Client1CWrapper
{
    /**
     * @var \Example\Client1C\Client1CInterface
     */
    private $client;

    /**
     * Client1CWrapper constructor
     *
     * @param \Example\Client1C\Client1CInterface $client
     */
    public function __construct(Client1CInterface $client)
    {
        $this->client = $client;
    }

    /**
     * send sends request and returns response
     *
     * @param \Example\Client1C\Request $request
     * @return \Example\Client1C\Response
     * @throws \Example\Client1C\Exceptions\MethodInvocationException
     * @throws \Example\Client1C\Exceptions\UnsupportedRequestException
     */
    public function send(Request $request)
    {
        if ($request instanceof RequestAddGoodsToOrder) {
            return $this->client->addGoodsToOrder($request);
        }

        if ($request instanceof RequestAddGoodToOrder) {
            return $this->client->addGoodToOrder($request);
        }

        if ($request instanceof RequestAddOrder) {
            return $this->client->addOrder($request);
        }

        if ($request instanceof RequestConfirmOrder) {
            return $this->client->confirmOrder($request);
        }

        if ($request instanceof RequestResetOrder) {
            return $this->client->resetOrder($request);
        }

        throw new UnsupportedRequestException($request);
    }
}
