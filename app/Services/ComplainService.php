<?php

namespace App\Services;

use App\Models\Complain;
use App\Repositories\ComplainRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Mail;
use App\Mail\CustomEmail;

class ComplainService
{
    protected ComplainRepository $complainRepository;

    public function __construct(ComplainRepository $complainRepository)
    {
        $this->complainRepository = $complainRepository;
    }

    /**
     * Create a new complain
     * @param array $data
     * @return Complain
     */
    public function createComplain(array $data): Complain
    {
        // Process and validate data
        $complainData = [
            'cname' => $data['cname'],
            'tele' => $data['tele'],
            'complain' => $data['complain'],
            'complain_date' => $data['complain_date'] ?? now()->toDateString(),
            'img1' => $data['img1'] ?? null,
            'img2' => $data['img2'] ?? null,
            'img3' => $data['img3'] ?? null,
        ];

        // Create the complain
        $complain = $this->complainRepository->createComplain($complainData);

        // Send notification email
        $this->sendComplainNotification($complain);

        return $complain;
    }

    /**
     * Get all complains with pagination
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedComplains(int $perPage = 15): LengthAwarePaginator
    {
        return $this->complainRepository->paginate($perPage);
    }

    /**
     * Get complain by ID
     * @param int $complainId
     * @return Complain
     */
    public function getComplainById(int $complainId): Complain
    {
        return $this->complainRepository->findOrFail($complainId);
    }

    /**
     * Update complain
     * @param int $complainId
     * @param array $data
     * @return Complain
     */
    public function updateComplain(int $complainId, array $data): Complain
    {
        return $this->complainRepository->update($complainId, $data);
    }

    /**
     * Delete complain
     * @param int $complainId
     * @return bool
     */
    public function deleteComplain(int $complainId): bool
    {
        return $this->complainRepository->delete($complainId);
    }

    /**
     * Search complains by term
     * @param string $searchTerm
     * @return Collection
     */
    public function searchComplains(string $searchTerm): Collection
    {
        return $this->complainRepository->searchComplains($searchTerm);
    }

    /**
     * Get complains by customer
     * @param string $customerName
     * @return Collection
     */
    public function getComplainsByCustomer(string $customerName): Collection
    {
        return $this->complainRepository->getComplainsByCustomerName($customerName);
    }

    /**
     * Get complains by phone number
     * @param string $phone
     * @return Collection
     */
    public function getComplainsByPhone(string $phone): Collection
    {
        return $this->complainRepository->getComplainsByPhone($phone);
    }

    /**
     * Get complains by date range
     * @param string $startDate
     * @param string $endDate
     * @return Collection
     */
    public function getComplainsByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->complainRepository->getComplainsByDateRange($startDate, $endDate);
    }

    /**
     * Get recent complains
     * @param int $limit
     * @return Collection
     */
    public function getRecentComplains(int $limit = 10): Collection
    {
        return $this->complainRepository->getRecentComplains($limit);
    }

    /**
     * Get complain statistics
     * @return array
     */
    public function getComplainStatistics(): array
    {
        $today = now()->toDateString();
        $thisMonth = now()->format('Y-m');
        
        return [
            'total_complains' => $this->complainRepository->getTotalComplainsCount(),
            'today_complains' => $this->complainRepository->getComplainsCountByDate($today),
            'recent_complains' => $this->complainRepository->getRecentComplains(5),
            'this_month_complains' => $this->getComplainsCountByMonth($thisMonth),
        ];
    }

    /**
     * Get complains count by month
     * @param string $month (Y-m format)
     * @return int
     */
    public function getComplainsCountByMonth(string $month): int
    {
        $startDate = $month . '-01';
        $endDate = date('Y-m-t', strtotime($startDate));
        
        return $this->complainRepository->getComplainsByDateRange($startDate, $endDate)->count();
    }

    /**
     * Send notification email for new complain
     * @param Complain $complain
     * @return bool
     */
    private function sendComplainNotification(Complain $complain): bool
    {
        try {
            $message = "New complain received from {$complain->cname}. Complain: {$complain->complain}";
            // You can customize this to send to admin email
            $adminEmail = config('mail.admin_email', 'admin@example.com');
            
            Mail::to($adminEmail)->send(new CustomEmail($message));
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to send complain notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Export complains to array for reports
     * @param array $filters
     * @return array
     */
    public function exportComplains(array $filters = []): array
    {
        $complains = $this->complainRepository->all();
        
        // Apply filters if provided
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $complains = $this->complainRepository->getComplainsByDateRange(
                $filters['start_date'], 
                $filters['end_date']
            );
        }
        
        if (!empty($filters['search'])) {
            $complains = $this->complainRepository->searchComplains($filters['search']);
        }

        return $complains->toArray();
    }
}