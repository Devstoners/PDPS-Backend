<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Repositories\NewsRepository;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $repository;
    public function __construct(NewsRepository $repository)
    {
        $this->repository = $repository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $news = News::select('id', 'news_en', 'news_si','news_ta','priority')->get();
        $response = [
            "AllNews" => $news,
        ];
        return response($response, 200);
        //return News::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $request = $request->validate([
            'newsSinhala' => 'required',
            'newsEnglish' => 'required',
            'newsTamil' => 'required',
        ]);
        $responce = $this->repository->addNews($request);
        return response($responce, 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'newsSinhala' => 'required',
            'newsEnglish' => 'required',
            'newsTamil' => 'required',
        ]);

        $response = $this->repository->updateNews($id, $request);

        return response($response, 200);
    }
    public function destroy($id)
    {
        $result = $this->repository->deleteNews($id);

        if ($result) {
            return response()->json(['message' => 'News deleted successfully.']);
        }
        return response()->json(['message' => 'News not found.'], 404);
    }


    public function count()
    {
        $count = $this->repository->getCount();
        $response = [
            "count" => $count,
        ];
        return response($response, 200);
    }

    public function viewSite(Request $request)
    {
        $request->validate([
            'language' => 'required|in:en,si,ta',
        ]);
        $language = $request->input('language');

        try {
            $news = $this->repository->getSiteView($language);
            return response()->json($news);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch news.'], 500);
        }

    }
}
