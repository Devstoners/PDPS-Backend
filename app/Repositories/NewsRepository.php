<?php

namespace App\Repositories;
use App\Models\News;


class NewsRepository
{
    public function addNews($data)
    {
        $news = News::create([
            'news_si' => $data['newsSinhala'],
            'news_en' => $data['newsEnglish'],
            'news_ta' => $data['newsTamil'],
            //'visibility' => $data['isChecked'],
            //'priority' => $data['isPriority'],
            'updated_at' => now(),
            'created_at' => now(),
        ]);
//        $newsId = News::latest()->first()->id;;
//        $detailNews = NewsLocale::create([
//            'news_id' => $newsId,
        return response([
            'news' => $news
        ], 200);
    }

    public function updateNews($id, $data)
    {
        $news = News::find($id);
        if (!$news) {
            return response(['message' => 'News not found.'], 404);
        }
        // Get the new updating priority value
//        $newPriority = $data['priority'] ?? 1;
//        $newPriority = $data['priority'];
        $newPriority = isset($data['priority']) ? $data['priority'] : $news->priority;
        $priority = News::where('id', $id)->value('priority');

        if($newPriority!=$priority) {
            // Check if the new priority exists in other records
            $existingNews = News::where('priority', $newPriority)->first();

            if ($existingNews) {
                // Update the existing news record with the new priority and adjust other records' priorities
                if ($newPriority == 1) {
                    News::where('priority', 1)->update(['priority' => null]);
                } elseif ($newPriority == 2) {
                    News::where('priority', 2)->update(['priority' => null]);
                } elseif ($newPriority == 3) {
                    News::where('priority', 3)->update(['priority' => null]);
                }

                $visibility = isset($data['visibility']) ? $data['visibility'] : $news->visibility;

                $news->update([
                    'news_si' => $data['newsSinhala'],
                    'news_en' => $data['newsEnglish'],
                    'news_ta' => $data['newsTamil'],
//                    'visibility' => $visibility,
                    'priority' => $newPriority,
                ]);
            } else {
                // Update the existing news record with the new priority
                $visibility = isset($data['visibility']) ? $data['visibility'] : $news->visibility;
                $news->update([
                    'news_si' => $data['newsSinhala'],
                    'news_en' => $data['newsEnglish'],
                    'news_ta' => $data['newsTamil'],
//                    'visibility' => $visibility,
                    'priority' => $newPriority,
                ]);
            }
        }else{
            $visibility = isset($data['visibility']) ? $data['visibility'] : $news->visibility;
            $news->update([
                'news_si' => $data['newsSinhala'],
                'news_en' => $data['newsEnglish'],
                'news_ta' => $data['newsTamil'],
//                'visibility' => $visibility,
            ]);
        }
        return response(['message' => 'News updated successfully.'], 200);
    }

    public function deleteNews($id)
    {
        $news = News::find($id);

        if ($news) {
            $news->delete();
            return true;
        }
        return false;
    }

    public function getCount()
    {
//        return News::where('visibility', true)->count();
        return News::count();
    }

    public function getSiteView($language)
    {
        try {
            $news = News::orderBy('priority', 'asc')->select("news_$language as news")->get();
            return $news;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
