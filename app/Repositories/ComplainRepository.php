<?php

namespace App\Repositories;

use App\Models\Complain;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class ComplainRepository extends BaseRepository
{
    /**
     * Specify Model class name
     * @return string
     */
    public function model(): string
    {
        return Complain::class;
    }

    /**
     * Create a new complain
     * @param array $data
     * @return Complain
     */
    public function createComplain(array $data): Complain
    {
        return $this->create([
            'cname' => $data['cname'],
            'tele' => $data['tele'],
            'complain' => $data['complain'],
            'complain_date' => $data['complain_date'],
            'img1' => $data['img1'] ?? null,
            'img2' => $data['img2'] ?? null,
            'img3' => $data['img3'] ?? null,
        ]);
    }

    /**
     * Get complains by date range
     * @param string $startDate
     * @param string $endDate
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function getComplainsByDateRange(string $startDate, string $endDate, array $columns = ['*'], array $relations = []): Collection
    {
        return $this->where_callback(function($query) use ($startDate, $endDate) {
            return $query->whereBetween('complain_date', [$startDate, $endDate]);
        })->getQuery()->with($relations)->get($columns);
    }

    /**
     * Get complains by phone number
     * @param string $phone
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function getComplainsByPhone(string $phone, array $columns = ['*'], array $relations = []): Collection
    {
        return $this->findByCriteria(['tele' => $phone], $columns, $relations);
    }

    /**
     * Get complains by customer name
     * @param string $name
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function getComplainsByCustomerName(string $name, array $columns = ['*'], array $relations = []): Collection
    {
        return $this->where_callback(function($query) use ($name) {
            return $query->where('cname', 'LIKE', "%{$name}%");
        })->getQuery()->with($relations)->get($columns);
    }

    /**
     * Get recent complains
     * @param int $limit
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function getRecentComplains(int $limit = 10, array $columns = ['*'], array $relations = []): Collection
    {
        return $this->latest($limit, $columns, $relations);
    }

    /**
     * Get total complains count
     * @return int
     */
    public function getTotalComplainsCount(): int
    {
        return $this->count();
    }

    /**
     * Get complains count by date
     * @param string $date
     * @return int
     */
    public function getComplainsCountByDate(string $date): int
    {
        return $this->count(['complain_date' => $date]);
    }

    /**
     * Search complains by content
     * @param string $searchTerm
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function searchComplains(string $searchTerm, array $columns = ['*'], array $relations = []): Collection
    {
        return $this->where_callback(function($query) use ($searchTerm) {
            return $query->where('complain', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('cname', 'LIKE', "%{$searchTerm}%");
        })->getQuery()->with($relations)->get($columns);
    }
}


