<?php

namespace App\Database;

use App\Exception\MissingArgumentException;

abstract class AbstractConnection
{
    protected $connection;
    protected $credential;

    const REQUIRED_CONNECTION_KEYS = [
        'driver',
        'host',
        'port',
        'db_name',
        'db_username',
        'db_user_password',
        'default_fetch'
    ];

    public function __construct(array $credential)
    {
        $this->credential = $credential;

        if (!$this->credentialsHaveRequiredKeys($credential)) {
            throw new MissingArgumentException(
                [],
                sprintf('Database connection credentials are not mapped correctly, required key: %s', implode(',', static::REQUIRED_CONNECTION_KEYS))
            );
        }
    }

    private function credentialsHaveRequiredKeys(array $credentials): bool
    {
        $matches = array_intersect_key(static::REQUIRED_CONNECTION_KEYS, array_keys($credentials));

        return count($matches) === count(static::REQUIRED_CONNECTION_KEYS);
    }

    abstract protected function parseCredential(array $credentials): array;
}
