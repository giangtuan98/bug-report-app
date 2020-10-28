<?php

namespace App\Database;

use App\Contracts\DatabaseConnectionInterface;
use App\Exception\NotFoundException;
use InvalidArgumentException;
use PHPUnit\Util\InvalidArgumentHelper;
use App\Database\Query;

abstract class QueryBuilder
{
    use Query;

    protected $connection; //pdo or mysqli

    protected $table;
    protected $statement;
    protected $fields;
    protected $placeholders;
    protected $bindings; //name = ? ['terry]
    protected $operation = self::DML_TYPE_SELECT; //dml - SELECT, UPDATE, INSERT, DELETE

    const OPERATORS = ['=', '>=', '>', '<=', '<', '<>'];
    const PLACEHOLDER = '?';
    const COLUMNS = '*';
    const DML_TYPE_SELECT = 'SELECT';
    const DML_TYPE_INSERT = 'INSERT';
    const DML_TYPE_UPDATE = 'UPDATE';
    const DML_TYPE_DELETE = 'DELETE';

    public function __construct(DatabaseConnectionInterface $databaseConnection)
    {
        $this->connection = $databaseConnection->getConnection();
    }

    public function table($table)
    {
        $this->table = $table;

        return $this;
    }

    // where('name', '=', 'giang')
    // where('name', 'giang')
    public function where($column, $operator = self::OPERATORS['0'], $value = null)
    {
        if (!in_array($operator, self::OPERATORS)) {
            if ($value == null) {
                // where ('name', 'giang') => where ('name', '=', 'giang')
                $value = $operator;
                $operator = self::OPERATORS[0];
            } else {
                throw new NotFoundException(['operator' => $operator], 'Operator is not valid');
            }
        }
        $this->parseWhere([$column => $value], $operator);

        return $this;
    }

    public function select(string $fields = self::COLUMNS)
    {
        $this->operation = self::DML_TYPE_SELECT;
        $this->fields = $fields;

        return $this;
    }

    /**
     * $conditions = [
     *      'name' => 'giang'
     * ],
     * $operator = '='
     * 
     * -> placeholders = [
     *      'name = ?'
     * ]
     * -> bindings = [
     *      'giang
     * ]
     */
    public function parseWhere(array $conditions, string $operator)
    {
        foreach ($conditions as $column => $value) {
            $this->placeholders[] = sprintf('%s %s %s', $column, $operator, self::PLACEHOLDER);
            $this->bindings[] = $value;
        }

        return $this;
    }

    /**
     * [
     *      'email' => 'giang.vu@impl.vn',
     *      'link' => 'http://abc.vn',
     * ]
     * 
     * fields = '`email`, `link`'
     * placeholders = [
     *      ?,
     *      ?,
     * ]
     * 
     * bindings = [
     *      'giang.vu@impl.vn',
     *      'http://abc.vn',
     * ]
     * 
     * query = INSERT INTO table (`email`, `link`) VALUES (?, ?)
     */
    public function create(array $data)
    {
        $this->fields = '`' . implode('`, `', array_keys($data)) . '`';

        foreach ($data as $value) {
            $this->placeholders[] = self::PLACEHOLDER;
            $this->bindings[] = $value;
        }

        $query = $this->prepare($this->getQuery(self::DML_TYPE_INSERT));
        $this->statement = $this->execute($query);

        return $this->lastInsertedId();
    }

    public function update(array $data)
    {
        $this->operation = self::DML_TYPE_UPDATE;

        $this->fields = [];

        foreach ($data as $column => $value) {
            $this->fields[] = sprintf('%s%s%s', $column, self::OPERATORS[0], "'$value'");
        }

        // $query = $this->prepare($this->getQuery(self::DML_TYPE_UPDATE));
        // $this->statement = $this->execute($query);

        return $this;
    }

    public function delete()
    {
        $this->operation = self::DML_TYPE_DELETE;

        return $this;
    }

    public function raw($query)
    {
        $query = $this->prepare($query);
        $this->statement = $this->execute($query);

        return $this;
    }

    public function find($id)
    {
        return $this->where('id', $id)->runQuery()->first();
    }

    public function findOneBy(string $field, $value)
    {
        return $this->where($field, $value)->runQuery()->first();
    }

    public function first()
    {
        return $this->count() ? $this->get()[0] : null;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function rollback(): void
    {
        $this->connection->rollback();
    }

    public function runQuery()
    {
        $query = $this->prepare($this->getQuery($this->operation));
        $this->statement = $this->execute($query);

        return $this;
    }
    abstract public function get();
    abstract public function count();
    abstract public function lastInsertedId();
    abstract public function prepare($query);
    abstract public function execute($statement);
    abstract public function fetchInto($className);
    abstract public function beginTransaction();
    abstract public function affected();
}
