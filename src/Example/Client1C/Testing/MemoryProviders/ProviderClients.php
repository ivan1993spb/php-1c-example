<?php

namespace Example\Client1C\Testing\MemoryProviders;

use Example\Client1C\Testing\Exceptions\ClientNotFoundException;
use Example\Client1C\Testing\Exceptions\InvalidUUIDException;
use Example\Client1C\Testing\Exceptions\ReservedINNException;
use Example\Client1C\Testing\ProviderInterfaces\ProviderClient as ProviderClientInterface;
use Example\Client1C\Testing\ProviderInterfaces\ProviderClients as ProviderClientsInterface;
use Ramsey\Uuid\Uuid;

/**
 * Class ProviderClients
 *
 * @package Example\Client1C\Testing\MemoryProviders
 */
class ProviderClients implements ProviderClientsInterface
{
    /**
     * @var \Example\Client1C\Testing\ProviderInterfaces\ProviderClient[]
     */
    private $clients = [];

    /**
     * @var bool
     */
    private $clientAlwaysExists = false;

    /**
     * @var ProviderClientFactory|null $providerClientFactory
     */
    private $providerClientFactory = null;

    /**
     * ProviderClients constructor
     *
     * @param bool $clientAlwaysExists
     */
    public function __construct($clientAlwaysExists = false, ProviderClientFactory $providerClientFactory = null)
    {
        $this->clientAlwaysExists = $clientAlwaysExists;
        $this->providerClientFactory = $providerClientFactory;
    }

    /**
     * @param \Example\Client1C\Testing\ProviderInterfaces\ProviderClient $client
     * @return \Example\Client1C\Testing\ProviderInterfaces\ProviderClients
     */
    public function addClient(ProviderClientInterface $client)
    {
        foreach ($this->clients as $clientExists) {
            if ($client->UUID() == $clientExists->UUID()) {
                // Return if client with the same UUID already exists
                return $this;
            }
        }

        array_push($this->clients, $client);

        return $this;
    }

    /**
     * @param string $clientUUID
     * @return \Example\Client1C\Testing\ProviderInterfaces\ProviderClient
     * @throws \Example\Client1C\Testing\Exceptions\InvalidUUIDException
     * @throws \Example\Client1C\Testing\Exceptions\ClientNotFoundException
     */
    public function getClient($clientUUID)
    {
        if (!Uuid::isValid($clientUUID)) {
            throw new InvalidUUIDException();
        }

        foreach ($this->clients as $client) {
            if ($client->UUID() === $clientUUID) {
                return $client;
            }
        }

        // В тестовом режиме может пригодиться такое поведение провайдера клиентов, когда провайдер
        // делает вид, что клиент всегда существет. Если клиент не существует, то провайдер добавляет
        // клиента самостоятельно
        if ($this->clientAlwaysExists) {
            /** @var ProviderClient $client */
            $client = null;

            if (empty($this->providerClientFactory)) {
                $client = new ProviderClient($clientUUID);
            } else {
                $client = $this->providerClientFactory->create($clientUUID);
            }

            if (!empty($client)) {
                array_push($this->clients, $client);
                return $client;
            }
        }

        throw new ClientNotFoundException();
    }

    /**
     * @param string|null $INN
     * @return ProviderClientInterface
     * @throws \Example\Client1C\Testing\Exceptions\ReservedINNException
     */
    public function makeClient($INN = null): ProviderClientInterface
    {
        $client = null;
        $UUID = Uuid::getFactory()->uuid4()->toString();

        if (!empty($INN)) {
            foreach ($this->clients as $client) {
                if ($client->INN() == $INN) {
                    throw new ReservedINNException();
                }
            }
        }

        if (empty($this->providerClientFactory)) {
            $client = new ProviderClient($UUID, $INN);
        } else {
            $client = $this->providerClientFactory->create($UUID, $INN);
        }

        array_push($this->clients, $client);

        return $client;
    }
}
