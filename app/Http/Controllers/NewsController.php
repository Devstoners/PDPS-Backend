<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Repositories\NewsRepository;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

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
     * @OA\Get(
     *     path="/news",
     *     tags={"News"},
     *     summary="Get all news",
     *     description="Retrieve a list of all news articles",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="News retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="AllNews", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="news_en", type="string", example="English news content"),
     *                 @OA\Property(property="news_si", type="string", example="Sinhala news content"),
     *                 @OA\Property(property="news_ta", type="string", example="Tamil news content"),
     *                 @OA\Property(property="priority", type="integer", example=1)
     *             ))
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
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
     * @OA\Post(
     *     path="/news",
     *     tags={"News"},
     *     summary="Create a new news article",
     *     description="Add a new news article to the system",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"newsSinhala", "newsEnglish", "newsTamil"},
     *             @OA\Property(property="newsSinhala", type="string", example="සිංහල පුවත් අන්තර්ගතය"),
     *             @OA\Property(property="newsEnglish", type="string", example="English news content"),
     *             @OA\Property(property="newsTamil", type="string", example="தமிழ் செய்தி உள்ளடக்கம்"),
     *             @OA\Property(property="priority", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="News article created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="News article created successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
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

    /**
     * @OA\Put(
     *     path="/news/{id}",
     *     tags={"News"},
     *     summary="Update a news article",
     *     description="Update an existing news article",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="News article ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"newsSinhala", "newsEnglish", "newsTamil"},
     *             @OA\Property(property="newsSinhala", type="string", example="Updated Sinhala news content"),
     *             @OA\Property(property="newsEnglish", type="string", example="Updated English news content"),
     *             @OA\Property(property="newsTamil", type="string", example="Updated Tamil news content"),
     *             @OA\Property(property="priority", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="News article updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="News article updated successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="News article not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
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
    /**
     * @OA\Delete(
     *     path="/news/{id}",
     *     tags={"News"},
     *     summary="Delete a news article",
     *     description="Delete an existing news article",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="News article ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="News article deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="News deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="News article not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="News not found.")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $result = $this->repository->deleteNews($id);

        if ($result) {
            return response()->json(['message' => 'News deleted successfully.']);
        }
        return response()->json(['message' => 'News not found.'], 404);
    }


    /**
     * @OA\Get(
     *     path="/newsCount",
     *     tags={"News"},
     *     summary="Get news count",
     *     description="Get the total count of news articles",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="News count retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="count", type="integer", example=25)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function count()
    {
        $count = $this->repository->getCount();
        $response = [
            "count" => $count,
        ];
        return response($response, 200);
    }

    /**
     * @OA\Get(
     *     path="/siteNewsView",
     *     tags={"News"},
     *     summary="Get news for site view",
     *     description="Get news articles for public site view in specified language",
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="Language code",
     *         required=true,
     *         @OA\Schema(type="string", enum={"en", "si", "ta"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="News retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
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
