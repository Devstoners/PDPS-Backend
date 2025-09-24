<?php

namespace App\Repositories\Contracts;

use App\Models\Complain;
use Illuminate\Database\Eloquent\Collection;

interface ComplainRepositoryInterface extends RepositoryInterface
{
    /**
     * Add a new complaint
     */
    public function addComplain(array $data): array;

    /**
     * Get complaints by status
     */
    public function getComplaintsByStatus(string $status): Collection;

    /**
     * Get complaints by date range
     */
    public function getComplaintsByDateRange(string $startDate, string $endDate): Collection;

    /**
     * Update complaint status
     */
    public function updateComplaintStatus(int $id, string $status): bool;

    /**
     * Get complaint count
     */
    public function getComplaintCount(): int;
}