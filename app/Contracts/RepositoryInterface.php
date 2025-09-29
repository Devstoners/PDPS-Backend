<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Interface RepositoryInterface
 * @package App\Contracts
 */
interface RepositoryInterface
{
    /**
     * Get all records
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function all(array $columns = ['*'], array $relations = []): Collection;

    /**
     * Get paginated records
     * @param int $perPage
     * @param array $columns
     * @param array $relations
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, array $columns = ['*'], array $relations = []): LengthAwarePaginator;

    /**
     * Find a record by ID
     * @param int $id
     * @param array $columns
     * @param array $relations
     * @return Model|null
     */
    public function find(int $id, array $columns = ['*'], array $relations = []): ?Model;

    /**
     * Find a record by ID or fail
     * @param int $id
     * @param array $columns
     * @param array $relations
     * @return Model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail(int $id, array $columns = ['*'], array $relations = []): Model;

    /**
     * Find records by criteria
     * @param array $criteria
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function findByCriteria(array $criteria, array $columns = ['*'], array $relations = []): Collection;

    /**
     * Find first record by criteria
     * @param array $criteria
     * @param array $columns
     * @param array $relations
     * @return Model|null
     */
    public function findFirstByCriteria(array $criteria, array $columns = ['*'], array $relations = []): ?Model;

    /**
     * Create a new record
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model;

    /**
     * Update a record
     * @param int $id
     * @param array $data
     * @return Model
     */
    public function update(int $id, array $data): Model;

    /**
     * Delete a record
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Update or create a record
     * @param array $attributes
     * @param array $values
     * @return Model
     */
    public function updateOrCreate(array $attributes, array $values = []): Model;

    /**
     * Count records by criteria
     * @param array $criteria
     * @return int
     */
    public function count(array $criteria = []): int;

    /**
     * Check if record exists by criteria
     * @param array $criteria
     * @return bool
     */
    public function exists(array $criteria): bool;

    /**
     * Get records with specific where clause
     * @param string $column
     * @param mixed $value
     * @param string $operator
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function where(string $column, $value, string $operator = '=', array $columns = ['*'], array $relations = []): Collection;

    /**
     * Get records with whereIn clause
     * @param string $column
     * @param array $values
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function whereIn(string $column, array $values, array $columns = ['*'], array $relations = []): Collection;

    /**
     * Order records by column
     * @param string $column
     * @param string $direction
     * @return mixed
     */
    public function orderBy(string $column, string $direction = 'asc');

    /**
     * Get latest records
     * @param int $limit
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function latest(int $limit = 10, array $columns = ['*'], array $relations = []): Collection;

    /**
     * Get oldest records
     * @param int $limit
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function oldest(int $limit = 10, array $columns = ['*'], array $relations = []): Collection;
}