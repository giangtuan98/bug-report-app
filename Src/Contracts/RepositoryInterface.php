<?php

namespace App\Contracts;

use App\Models\Model;

interface RepositoryInterface
{
    public function find(int $id): ?object;
    public function findOneBy(string $field, int $id): ?object;
    public function findBy(array $criteria);
    public function findAll(): array;
    public function sql(string $query);
    public function create(Model $model): object;
    public function update(Model $model, array $condition = []): ?object;
    public function delete(Model $model, array $condition = []): void;
}
