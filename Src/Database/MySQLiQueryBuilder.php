<?php

namespace App\Database;

use App\Exception\NotFoundException;
use InvalidArgumentException;
use PDO;
use ReflectionClass;

class MySQLiQueryBuilder extends QueryBuilder
{
    private $resultSet;
    private $results;

    const PARAM_TYPE_INT = 'i';
    const PARAM_TYPE_STRING = 's';
    const PARAM_TYPE_DOUBLE = 'd';

    public function get()
    {
        $results = [];

        if (!$this->resultSet) {
            $this->resultSet = $this->statement->get_result();
            if ($this->resultSet) {
                while ($object = $this->resultSet->fetch_object()) {
                    $results[] = $object;
                }
            }
            $this->results = $results;
        }

        return $this->results;
    }

    public function count()
    {
        if (!$this->resultSet) {
            $this->get();
        }

        return $this->resultSet ? $this->resultSet->num_rows : false;
    }

    public function lastInsertedId()
    {
        return $this->connection->insert_id;
    }

    public function prepare($query)
    {
        return $this->connection->prepare($query);
    }

    public function execute($statement)
    {
        if (!$statement) {
            throw new InvalidArgumentException('MySQLi statement is false');
        }

        if ($this->bindings) {

            $binding = $this->parseBindings($this->bindings);
            $reflectionObj = new ReflectionClass('mysqli_stmt');
            $method = $reflectionObj->getMethod('bind_param');
            $method->invokeArgs($statement, $binding);
        }

        $statement->execute();
        $this->bindings = [];
        $this->placeholders = [];

        return $statement;
    }

    private function parseBindings(array $params)
    {
        $binding = [];
        $count = count($params);
        if ($count === 0) {
            return $this->bindings;
        }

        $bindingType = $this->parseBindingTypes(); // "sids"
        $bindings[] = &$bindingType;

        for ($i = 0; $i < $count; $i++) {
            $bindings[] = &$params[$i];
        }
        return $bindings;
    }

    public function parseBindingTypes()
    {
        $bindingTypes = [];
        foreach ($this->bindings as $binding) {
            if (is_int($binding)) {
                $bindingTypes[] = self::PARAM_TYPE_INT;
            }

            if (is_string($binding)) {
                $bindingTypes[] = self::PARAM_TYPE_STRING;
            }

            if (is_float($binding)) {
                $bindingTypes[] = self::PARAM_TYPE_DOUBLE;
            }
        }

        return implode('', $bindingTypes);
    }

    public function fetchInto($className)
    {
        $results = [];
        $this->resultSet = $this->statement->get_result();
        while ($object = $this->resultSet->fetch_object($className)) {
            $results[] = $object;
        }

        return $this->results = $results;
    }

    public function beginTransaction()
    {
        $this->connection->begin_transaction();
    }

    public function affected()
    {
        return $this->statement->store_result();
        return $this->statement->affected_rows;
    }
}
