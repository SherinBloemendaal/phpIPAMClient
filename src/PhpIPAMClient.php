<?php

declare(strict_types=1);

namespace SherinBloemendaal\PhpIPAMClient;

use SherinBloemendaal\PhpIPAMClient\Connection\Connection;

class PhpIPAMClient
{
    protected Connection $connection;

    public function __construct(string $url, string $appID, string $username, string $password, string $apiKey, string $securityMethod = Connection::SECURITY_METHOD_SSL)
    {
        $this->connection = Connection::initializeConnection($url, $appID, $username, $password, $apiKey, $securityMethod);
    }

    public function call(string $method, string $controller, array $identifiers = [], array $params = []): mixed
    {
        return $this->connection->call($method, $controller, $identifiers, $params);
    }

    public function getAllControllers(): mixed
    {
        return $this->call('OPTIONS', '')->getData();
    }

    public function getToken(): ?string
    {
        return $this->connection->getToken();
    }

    public function getTokenExpires(): ?string
    {
        return $this->connection->getTokenExpires();
    }

    public function getAllUsers(): mixed
    {
        return $this->call('get', 'user', ['all'])->getData();
    }

    public function getAllAdmins(): mixed
    {
        return $this->call('admins', 'user', ['all'])->getData();
    }
}
