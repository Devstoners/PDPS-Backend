<?php

namespace App\Repositories;

use App\Models\Project;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class ProjectRepository extends BaseRepository
{
    /**
     * Specify Model class name
     * @return string
     */
    public function model(): string
    {
        return Project::class;
    }

    /**
     * Get projects with locales
     * @param array $columns
     * @return Collection
     */
    public function getWithLocales(array $columns = ['*']): Collection
    {
        return $this->all($columns, ['locales']);
    }

    /**
     * Get active projects
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function getActiveProjects(array $columns = ['*'], array $relations = []): Collection
    {
        return $this->findByCriteria(['status' => 'active'], $columns, $relations);
    }

    /**
     * Get completed projects
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function getCompletedProjects(array $columns = ['*'], array $relations = []): Collection
    {
        return $this->findByCriteria(['status' => 'completed'], $columns, $relations);
    }

    /**
     * Search projects by title
     * @param string $title
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function searchByTitle(string $title, array $columns = ['*'], array $relations = []): Collection
    {
        return $this->where_callback(function($query) use ($title) {
            return $query->where('title', 'LIKE', "%{$title}%");
        })->getQuery()->with($relations)->get($columns);
    }

    /**
     * Get projects by date range
     * @param string $startDate
     * @param string $endDate
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function getByDateRange(string $startDate, string $endDate, array $columns = ['*'], array $relations = []): Collection
    {
        return $this->where_callback(function($query) use ($startDate, $endDate) {
            return $query->whereBetween('created_at', [$startDate, $endDate]);
        })->getQuery()->with($relations)->get($columns);
    }

    /**
     * Get recent projects
     * @param int $limit
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function getRecentProjects(int $limit = 10, array $columns = ['*'], array $relations = []): Collection
    {
        return $this->latest($limit, $columns, $relations);
    }

    /**
     * Get projects count by status
     * @param string $status
     * @return int
     */
    public function getCountByStatus(string $status): int
    {
        return $this->count(['status' => $status]);
    }

    /**
     * Update project status
     * @param int $projectId
     * @param string $status
     * @return Project
     */
    public function updateStatus(int $projectId, string $status): Project
    {
        return $this->update($projectId, ['status' => $status]);
    }
}
