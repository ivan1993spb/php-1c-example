<?php

namespace Example\Client1C\Testing\ProviderInterfaces;

/**
 * Interface ProviderClients
 *
 * @package Example\Client1C\Testing\ProviderInterfaces
 */
interface ProviderClients
{
    /**
     * @param string $clientUUID
     * @return ProviderClient
     * @throws \Example\Client1C\Testing\Exceptions\InvalidUUIDException
     * @throws \Example\Client1C\Testing\Exceptions\ClientNotFoundException
     */
    public function getClient($clientUUID);

    /**
     * @param string $inn
     * @return ProviderClient
     * @throws \Example\Client1C\Testing\Exceptions\ReservedINNException
     */
    public function makeClient($inn): ProviderClient;

    /**
     * @param \Example\Client1C\Testing\ProviderInterfaces\ProviderClient $client
     * @return \Example\Client1C\Testing\ProviderInterfaces\ProviderClients
     */
    public function addClient(ProviderClient $client);
}
