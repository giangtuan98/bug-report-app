<?php

namespace App\Helpers;

use App\Database\MySQLiConnection;
use App\Database\MySQLiQueryBuilder;
use App\Database\PDOConnection;
use App\Database\PDOQueryBuilder;
use App\Database\QueryBuilder;
use App\Exception\DatabaseConnectionException;

class DBQueryBuilderFactory
{
    public static function make(
        string $credentialFile = 'database',
        string $connectionType = 'pdo',
        array $option = []
    ): QueryBuilder {
        $connection = null;

        $credentials = array_merge(Config::get($credentialFile, $connectionType), $option);
        switch ($connectionType) {
            case 'pdo':
                $connection = (new PDOConnection($credentials))->connect();
                return new PDOQueryBuilder($connection);
            case 'mysql':
                $connection = (new MySQLiConnection($credentials))->connect();
                return new MySQLiQueryBuilder($connection);
            default:
                throw new DatabaseConnectionException([], 'Connect method is not supported');
        }
    }

    public static function get() {
        $application = new App;

        if ($application->isTestMode()) {
            return self::make('database', 'pdo', ['db_name' => 'bug_app_testing']);
        }

        if (defined('PHPUNIT_RUNNING')) {
            // echo PHPUNIT
        }

        return self::make();
    }
}
