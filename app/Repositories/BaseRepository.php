<?php

namespace App\Repositories;

use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

abstract class BaseRepository implements RepositoryInterface
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get all records
     */
    public function all(): Collection
    {
        return $this->model->all();
    }

    /**
     * Find a record by ID
     */
    public function find(int $id): ?Model
    {
        return $this->model->find($id);
    }

    /**
     * Find a record by ID or throw exception
     */
    public function findOrFail(int $id): Model
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Create a new record
     */
    public function create(array $data): Model
    {
        try {
            return $this->model->create($data);
        } catch (\Exception $e) {
            throw new \Exception("Failed to create record: " . $e->getMessage());
        }
    }

    /**
     * Update a record
     */
    public function update(int $id, array $data): bool
    {
        try {
            $model = $this->find($id);
            if (!$model) {
                return false;
            }
            return $model->update($data);
        } catch (\Exception $e) {
            throw new \Exception("Failed to update record: " . $e->getMessage());
        }
    }

    /**
     * Delete a record
     */
    public function delete(int $id): bool
    {
        try {
            $model = $this->find($id);
            if (!$model) {
                return false;
            }
            return $model->delete();
        } catch (\Exception $e) {
            throw new \Exception("Failed to delete record: " . $e->getMessage());
        }
    }

    /**
     * Get records with pagination
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->paginate($perPage);
    }

    /**
     * Get records with conditions
     */
    public function where(string $column, $value): Collection
    {
        return $this->model->where($column, $value)->get();
    }

    /**
     * Get first record with conditions
     */
    public function whereFirst(string $column, $value): ?Model
    {
        return $this->model->where($column, $value)->first();
    }

    /**
     * Count records
     */
    public function count(): int
    {
        return $this->model->count();
    }

    /**
     * Check if record exists
     */
    public function exists(int $id): bool
    {
        return $this->model->where('id', $id)->exists();
    }

    /**
     * Get the model instance
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * Set the model instance
     */
    public function setModel(Model $model): self
    {
        $this->model = $model;
        return $this;
    }
}