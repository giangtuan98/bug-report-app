<?php

namespace App\Repositories;

use App\Contracts\RepositoryInterface;
use App\Database\QueryBuilder;
use App\Models\Model;

abstract class BaseRepository implements RepositoryInterface
{
    protected static $table;
    protected static $className;

    /**
     * @var QueryBuilder $queryBuilder
     */
    private $queryBuilder;

    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    public function find(int $id): ?object
    {
        return $this->findOneBy('id', $id);
    }

    public function findOneBy(string $field, $value): ?object
    {
        $result =  $this->queryBuilder->table(static::$table)->select()->where($field, $value)->runQuery()->fetchInto(static::$className);

        return $result ? $result[0] : null;
    }

    public function findBy(array $criteria)
    {
        $this->queryBuilder->table(static::$table)->select();

        foreach ($criteria as $criterion) {
            $this->queryBuilder->where(...$criterion);
        }

        return $this->queryBuilder->runQuery()->fetchInto(static::$className);
    }

    public function findAll(): array
    {
        return $this->queryBuilder->table(static::$table)->select()->runQuery()->fetchInto(static::$className);
    }

    public function sql(string $query)
    {
        return $this->queryBuilder->raw($query);
    }

    public function create(Model $model): object
    {
        $id = $this->queryBuilder->table(static::$table)->create($model->toArray());

        return $this->find($id);
    }

    public function update(Model $model, $condition = []): object
    {
        $this->queryBuilder->table(static::$table)->update($model->toArray());

        foreach ($condition as $column => $value) {
            $this->queryBuilder->where($column, $value);
        }

        $this->queryBuilder->where('id', $model->getId())->runQuery();

        return $this->find($model->getId());
    }

    public function delete(Model $model, array $condition = []): void
    {
        $this->queryBuilder->table(static::$table)->delete();

        foreach ($condition as $column => $value) {
            $this->queryBuilder->where($column, $value);
        }

        $this->queryBuilder->where('id', $model->getId())->runQuery();
    }
}
