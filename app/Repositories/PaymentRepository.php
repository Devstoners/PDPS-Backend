<?php

namespace App\Repositories;

use App\Models\Payments;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class PaymentRepository extends BaseRepository
{
    /**
     * Specify Model class name
     * @return string
     */
    public function model(): string
    {
        return Payments::class;
    }

    /**
     * Get payments by year
     * @param int $year
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function getByYear(int $year, array $columns = ['*'], array $relations = []): Collection
    {
        return $this->findByCriteria(['year' => $year], $columns, $relations);
    }

    /**
     * Get payments by tax ID
     * @param int $taxId
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function getByTaxId(int $taxId, array $columns = ['*'], array $relations = []): Collection
    {
        return $this->findByCriteria(['tax_id' => $taxId], $columns, $relations);
    }

    /**
     * Get payments by type
     * @param string $paymentType
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function getByType(string $paymentType, array $columns = ['*'], array $relations = []): Collection
    {
        return $this->findByCriteria(['payment_type' => $paymentType], $columns, $relations);
    }

    /**
     * Get payments by amount range
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
     * Get total payments for a year
     * @param int $year
     * @return float
     */
    public function getTotalPaymentsByYear(int $year): float
    {
        return $this->where_callback(function($query) use ($year) {
            return $query->where('year', $year);
        })->getQuery()->sum('amount') ?? 0;
    }

    /**
     * Get total payments by type
     * @param string $paymentType
     * @return float
     */
    public function getTotalPaymentsByType(string $paymentType): float
    {
        return $this->where_callback(function($query) use ($paymentType) {
            return $query->where('payment_type', $paymentType);
        })->getQuery()->sum('amount') ?? 0;
    }

    /**
     * Get payments with tax information
     * @param array $columns
     * @return Collection
     */
    public function getWithTax(array $columns = ['*']): Collection
    {
        return $this->all($columns, ['tax']);
    }

    /**
     * Get recent payments
     * @param int $limit
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function getRecentPayments(int $limit = 10, array $columns = ['*'], array $relations = []): Collection
    {
        return $this->latest($limit, $columns, $relations);
    }

    /**
     * Create payment record
     * @param array $data
     * @return Payments
     */
    public function createPayment(array $data): Payments
    {
        return $this->create([
            'year' => $data['year'],
            'amount' => $data['amount'],
            'tax_id' => $data['tax_id'],
            'payment_type' => $data['payment_type']
        ]);
    }
}