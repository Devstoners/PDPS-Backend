<?php

namespace App\Repositories;

use App\Models\Tax;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class TaxRepository extends BaseRepository
{
    /**
     * Specify Model class name
     * @return string
     */
    public function model(): string
    {
        return Tax::class;
    }

    /**
     * Find tax record by NIC
     * @param string $nic
     * @return Tax|null
     */
    public function findByNic(string $nic): ?Tax
    {
        return $this->findFirstByCriteria(['nic' => $nic]);
    }

    /**
     * Get tax records by account number
     * @param string $account
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function getByAccount(string $account, array $columns = ['*'], array $relations = []): Collection
    {
        return $this->findByCriteria(['account' => $account], $columns, $relations);
    }

    /**
     * Get tax records by amount range
     * @param float $minAmount
     * @param float $maxAmount
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function getByAmountRange(float $minAmount, float $maxAmount, array $columns = ['*'], array $relations = []): Collection
    {
        return $this->where_callback(function($query) use ($minAmount, $maxAmount) {
            return $query->whereBetween('amount', [$minAmount, $maxAmount]);
        })->getQuery()->with($relations)->get($columns);
    }

    /**
     * Search tax records by name
     * @param string $name
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function searchByName(string $name, array $columns = ['*'], array $relations = []): Collection
    {
        return $this->where_callback(function($query) use ($name) {
            return $query->where('name', 'LIKE', "%{$name}%");
        })->getQuery()->with($relations)->get($columns);
    }

    /**
     * Get total tax amount
     * @return float
     */
    public function getTotalTaxAmount(): float
    {
        return $this->getQuery()->sum('amount') ?? 0;
    }

    /**
     * Get tax records with payments
     * @param array $columns
     * @return Collection
     */
    public function getWithPayments(array $columns = ['*']): Collection
    {
        return $this->all($columns, ['payments']);
    }

    /**
     * Update or create tax record
     * @param string $nic
     * @param array $data
     * @return Tax
     */
    public function updateOrCreateByNic(string $nic, array $data): Tax
    {
        return $this->updateOrCreate(['nic' => $nic], $data);
    }
}