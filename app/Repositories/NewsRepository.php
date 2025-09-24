<?php

namespace App\Repositories;

use App\Models\News;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class NewsRepository extends BaseRepository
{
    /**
     * Specify Model class name
     * @return string
     */
    public function model(): string
    {
        return News::class;
    }

    /**
     * Create news with multilingual content
     * @param array $data
     * @return News
     */
    public function createNews(array $data): News
    {
        return $this->create([
            'news_si' => $data['newsSinhala'] ?? $data['news_si'] ?? null,
            'news_en' => $data['newsEnglish'] ?? $data['news_en'] ?? null,
            'news_ta' => $data['newsTamil'] ?? $data['news_ta'] ?? null,
            'visibility' => $data['visibility'] ?? true,
            'priority' => $data['priority'] ?? null,
            'status' => $data['status'] ?? 'published',
            'is_featured' => $data['is_featured'] ?? false,
        ]);
    }

    /**
     * Update news with priority management
     * @param int $newsId
     * @param array $data
     * @return News
     */
    public function updateNewsWithPriority(int $newsId, array $data): News
    {
        return DB::transaction(function () use ($newsId, $data) {
            $news = $this->findOrFail($newsId);
            $newPriority = $data['priority'] ?? $news->priority;

            // Handle priority conflicts
            if ($newPriority != $news->priority) {
                $this->handlePriorityConflict($newPriority);
            }

            $updateData = [
                'news_si' => $data['newsSinhala'] ?? $data['news_si'] ?? $news->news_si,
                'news_en' => $data['newsEnglish'] ?? $data['news_en'] ?? $news->news_en,
                'news_ta' => $data['newsTamil'] ?? $data['news_ta'] ?? $news->news_ta,
                'visibility' => $data['visibility'] ?? $news->visibility,
                'priority' => $newPriority,
            ];

            return $this->update($newsId, $updateData);
        });
    }

    /**
     * Get news by priority
     * @param int $priority
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function getByPriority(int $priority, array $columns = ['*'], array $relations = []): Collection
    {
        return $this->findByCriteria(['priority' => $priority], $columns, $relations);
    }

    /**
     * Get visible news
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function getVisibleNews(array $columns = ['*'], array $relations = []): Collection
    {
        return $this->findByCriteria(['visibility' => true], $columns, $relations);
    }

    /**
     * Get news ordered by priority
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function getOrderedByPriority(array $columns = ['*'], array $relations = []): Collection
    {
        return $this->orderBy('priority', 'asc')
                    ->getQuery()
                    ->with($relations)
                    ->get($columns);
    }

    /**
     * Get news for site view by language
     * @param string $language
     * @return Collection
     */
    public function getSiteViewByLanguage(string $language): Collection
    {
        $column = "news_{$language} as news";
        return $this->orderBy('priority', 'asc')
                    ->getQuery()
                    ->where('visibility', true)
                    ->select($column)
                    ->get();
    }

    /**
     * Get published news
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function getPublishedNews(array $columns = ['*'], array $relations = []): Collection
    {
        return $this->findByCriteria(['status' => 'published'], $columns, $relations);
    }

    /**
     * Get featured news
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function getFeaturedNews(array $columns = ['*'], array $relations = []): Collection
    {
        return $this->findByCriteria(['is_featured' => true], $columns, $relations);
    }

    /**
     * Search news by content
     * @param string $searchTerm
     * @param string $language
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function searchByContent(string $searchTerm, string $language = 'en', array $columns = ['*'], array $relations = []): Collection
    {
        $searchColumn = "news_{$language}";
        return $this->where_callback(function($query) use ($searchColumn, $searchTerm) {
            return $query->where($searchColumn, 'LIKE', "%{$searchTerm}%");
        })->getQuery()->with($relations)->get($columns);
    }

    /**
     * Get total news count
     * @return int
     */
    public function getTotalNewsCount(): int
    {
        return $this->count();
    }

    /**
     * Get visible news count
     * @return int
     */
    public function getVisibleNewsCount(): int
    {
        return $this->count(['visibility' => true]);
    }

    /**
     * Toggle news visibility
     * @param int $newsId
     * @return News
     */
    public function toggleVisibility(int $newsId): News
    {
        $news = $this->findOrFail($newsId);
        return $this->update($newsId, ['visibility' => !$news->visibility]);
    }

    /**
     * Set news priority
     * @param int $newsId
     * @param int $priority
     * @return News
     */
    public function setPriority(int $newsId, int $priority): News
    {
        $this->handlePriorityConflict($priority);
        return $this->update($newsId, ['priority' => $priority]);
    }

    /**
     * Handle priority conflicts
     * @param int $priority
     * @return void
     */
    private function handlePriorityConflict(int $priority): void
    {
        if (in_array($priority, [1, 2, 3])) {
            $this->model->where('priority', $priority)->update(['priority' => null]);
        }
    }

    /**
     * Clear all priorities
     * @return bool
     */
    public function clearAllPriorities(): bool
    {
        return $this->model->whereNotNull('priority')->update(['priority' => null]);
    }

    /**
     * Get news with highest priority
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function getTopPriorityNews(array $columns = ['*'], array $relations = []): Collection
    {
        return $this->where_callback(function($query) {
            return $query->whereNotNull('priority')
                        ->orderBy('priority', 'asc');
        })->getQuery()->with($relations)->get($columns);
    }
}
