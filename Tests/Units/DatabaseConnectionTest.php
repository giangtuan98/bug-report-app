<?php

use App\Contracts\DatabaseConnectionInterface;
use App\Database\MySQLiConnection;
use App\Database\PDOConnection;
use App\Exception\MissingArgumentException;
use App\Helpers\Config;
use PHPUnit\Framework\TestCase;

class DatabaseConnectionTest extends TestCase
{
    public function testItThrowMissingExceptionWithWrongCredentialKey()
    {
        self::expectException(MissingArgumentException::class);

        $credentials = [];
        $pdoHandler = (new PDOConnection($credentials))->connect();

        self::assertNotNull($pdoHandler);
    }

    public function testItCanConnectDatabaseWithPdoApi()
    {
        $credentials = $this->getCredentials('pdo');
        $pdoHandler = (new PDOConnection($credentials))->connect();

        self::assertInstanceOf(DatabaseConnectionInterface::class, $pdoHandler);
        return $pdoHandler;
    }

    /** @depends testItCanConnectDatabaseWithPdoApi */
    public function testItIsAValidPdoConnection(DatabaseConnectionInterface $handler)
    {
        self::assertInstanceOf(\PDO::class, $handler->getConnection());
    }

    public function testItCanConnectDatabaseWithMySQLiApi()
    {
        $credentials = $this->getCredentials('mysql');
        $handler = (new MySQLiConnection($credentials))->connect();

        self::assertInstanceOf(DatabaseConnectionInterface::class, $handler);
        return $handler;
    }

    /** @depends testItCanConnectDatabaseWithMySQLiApi */
    public function testItIsAValidMySQLiConnection(DatabaseConnectionInterface $handler)
    {
        self::assertInstanceOf(\mysqli::class, $handler->getConnection());
    }

    private function getCredentials(string $type)
    {
        return array_merge(
            Config::get('database', $type),
            ['db_name' => 'bug']
        );
    }
}
