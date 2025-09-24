<?php

namespace App\Repositories;

use App\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class BaseRepository
 * @package App\Repositories
 */
abstract class BaseRepository implements RepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @var Builder
     */
    protected $query;

    /**
     * BaseRepository constructor.
     */
    public function __construct()
    {
        $this->makeModel();
        $this->resetQuery();
    }

    /**
     * Specify Model class name
     * @return string
     */
    abstract public function model(): string;

    /**
     * @return Model
     * @throws \Exception
     */
    public function makeModel(): Model
    {
        $model = app($this->model());

        if (!$model instanceof Model) {
            throw new \Exception("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $this->model = $model;
    }

    /**
     * Reset the query builder
     */
    protected function resetQuery(): void
    {
        $this->query = $this->model->newQuery();
    }

    /**
     * Get all records
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function all(array $columns = ['*'], array $relations = []): Collection
    {
        $this->query = $this->query->with($relations);
        $result = $this->query->get($columns);
        $this->resetQuery();
        return $result;
    }

    /**
     * Get paginated records
     * @param int $perPage
     * @param array $columns
     * @param array $relations
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, array $columns = ['*'], array $relations = []): LengthAwarePaginator
    {
        $this->query = $this->query->with($relations);
        $result = $this->query->paginate($perPage, $columns);
        $this->resetQuery();
        return $result;
    }

    /**
     * Find a record by ID
     * @param int $id
     * @param array $columns
     * @param array $relations
     * @return Model|null
     */
    public function find(int $id, array $columns = ['*'], array $relations = []): ?Model
    {
        $this->query = $this->query->with($relations);
        $result = $this->query->find($id, $columns);
        $this->resetQuery();
        return $result;
    }

    /**
     * Find a record by ID or fail
     * @param int $id
     * @param array $columns
     * @param array $relations
     * @return Model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail(int $id, array $columns = ['*'], array $relations = []): Model
    {
        $this->query = $this->query->with($relations);
        $result = $this->query->findOrFail($id, $columns);
        $this->resetQuery();
        return $result;
    }

    /**
     * Find records by criteria
     * @param array $criteria
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function findByCriteria(array $criteria, array $columns = ['*'], array $relations = []): Collection
    {
        $this->query = $this->query->with($relations);
        
        foreach ($criteria as $field => $value) {
            if (is_array($value)) {
                $this->query = $this->query->whereIn($field, $value);
            } else {
                $this->query = $this->query->where($field, $value);
            }
        }
        
        $result = $this->query->get($columns);
        $this->resetQuery();
        return $result;
    }

    /**
     * Find first record by criteria
     * @param array $criteria
     * @param array $columns
     * @param array $relations
     * @return Model|null
     */
    public function findFirstByCriteria(array $criteria, array $columns = ['*'], array $relations = []): ?Model
    {
        $this->query = $this->query->with($relations);
        
        foreach ($criteria as $field => $value) {
            if (is_array($value)) {
                $this->query = $this->query->whereIn($field, $value);
            } else {
                $this->query = $this->query->where($field, $value);
            }
        }
        
        $result = $this->query->first($columns);
        $this->resetQuery();
        return $result;
    }

    /**
     * Create a new record
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        $result = $this->model->create($data);
        $this->resetQuery();
        return $result;
    }

    /**
     * Update a record
     * @param int $id
     * @param array $data
     * @return Model
     */
    public function update(int $id, array $data): Model
    {
        $record = $this->findOrFail($id);
        $record->update($data);
        $this->resetQuery();
        return $record->fresh();
    }

    /**
     * Delete a record
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $record = $this->findOrFail($id);
        $result = $record->delete();
        $this->resetQuery();
        return $result;
    }

    /**
     * Update or create a record
     * @param array $attributes
     * @param array $values
     * @return Model
     */
    public function updateOrCreate(array $attributes, array $values = []): Model
    {
        $result = $this->model->updateOrCreate($attributes, $values);
        $this->resetQuery();
        return $result;
    }

    /**
     * Count records by criteria
     * @param array $criteria
     * @return int
     */
    public function count(array $criteria = []): int
    {
        foreach ($criteria as $field => $value) {
            if (is_array($value)) {
                $this->query = $this->query->whereIn($field, $value);
            } else {
                $this->query = $this->query->where($field, $value);
            }
        }
        
        $result = $this->query->count();
        $this->resetQuery();
        return $result;
    }

    /**
     * Check if record exists by criteria
     * @param array $criteria
     * @return bool
     */
    public function exists(array $criteria): bool
    {
        foreach ($criteria as $field => $value) {
            if (is_array($value)) {
                $this->query = $this->query->whereIn($field, $value);
            } else {
                $this->query = $this->query->where($field, $value);
            }
        }
        
        $result = $this->query->exists();
        $this->resetQuery();
        return $result;
    }

    /**
     * Get records with specific where clause
     * @param string $column
     * @param mixed $value
     * @param string $operator
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function where(string $column, $value, string $operator = '=', array $columns = ['*'], array $relations = []): Collection
    {
        $this->query = $this->query->with($relations)->where($column, $operator, $value);
        $result = $this->query->get($columns);
        $this->resetQuery();
        return $result;
    }

    /**
     * Get records with whereIn clause
     * @param string $column
     * @param array $values
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function whereIn(string $column, array $values, array $columns = ['*'], array $relations = []): Collection
    {
        $this->query = $this->query->with($relations)->whereIn($column, $values);
        $result = $this->query->get($columns);
        $this->resetQuery();
        return $result;
    }

    /**
     * Order records by column
     * @param string $column
     * @param string $direction
     * @return $this
     */
    public function orderBy(string $column, string $direction = 'asc')
    {
        $this->query = $this->query->orderBy($column, $direction);
        return $this;
    }

    /**
     * Get latest records
     * @param int $limit
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function latest(int $limit = 10, array $columns = ['*'], array $relations = []): Collection
    {
        $this->query = $this->query->with($relations)->latest()->limit($limit);
        $result = $this->query->get($columns);
        $this->resetQuery();
        return $result;
    }

    /**
     * Get oldest records
     * @param int $limit
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function oldest(int $limit = 10, array $columns = ['*'], array $relations = []): Collection
    {
        $this->query = $this->query->with($relations)->oldest()->limit($limit);
        $result = $this->query->get($columns);
        $this->resetQuery();
        return $result;
    }

    /**
     * Apply custom query constraints
     * @param \Closure $callback
     * @return $this
     */
    public function where_callback(\Closure $callback)
    {
        $this->query = $callback($this->query);
        return $this;
    }

    /**
     * Get the query builder instance
     * @return Builder
     */
    public function getQuery(): Builder
    {
        return $this->query;
    }

    /**
     * Get the model instance
     * @return Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }
}