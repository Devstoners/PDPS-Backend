<?php

namespace App\Repositories;

use App\Models\Complain;
use App\Repositories\BaseRepository;
use App\Repositories\Contracts\ComplainRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class ComplainRepository extends BaseRepository implements ComplainRepositoryInterface
{
    public function __construct(Complain $model)
    {
        parent::__construct($model);
    }

    /**
     * Add a new complaint
     */
    public function addComplain(array $data): array
    {
        $complain = $this->create([
            'cname' => $data['cname'],
            'tele' => $data['tele'],
            'complain' => $data['complain'],
            'complain_date' => $data['complain_date'],
        ]);

        return [
            'Complain' => $complain,
        ];
    }

    /**
     * Get complaints by status
     */
    public function getComplaintsByStatus(string $status): Collection
    {
        return $this->model->where('status', $status)->get();
    }

    /**
     * Get complaints by date range
     */
    public function getComplaintsByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->model->whereBetween('complain_date', [$startDate, $endDate])->get();
    }

    /**
     * Update complaint status
     */
    public function updateComplaintStatus(int $id, string $status): bool
    {
        return $this->update($id, ['status' => $status]);
    }

    /**
     * Get complaint count
     */
    public function getComplaintCount(): int
    {
        return $this->count();
    }
}


