<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Models\GalleryImage;
use Illuminate\Http\Request;
use App\Repositories\GalleryRepository;
use Illuminate\Support\Facades\Validator;


class GalleryController extends Controller
{
    private $repository;
    public function __construct(GalleryRepository $repository)
    {
        $this->repository = $repository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//        $albums = Gallery::select('id', 'created_at','topic_en','topic_si','topic_ta')->get();
        $albums = Gallery::with(['images' => function ($query) {
            $query->select('image_path', 'gallery_id');
        }])->select('id', 'topic_en','topic_si','topic_ta','created_at')->get();
        $response = [
            "AllGalleries" => $albums,
        ];
        return response($response, 200);
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
//        \Log::info('Data :', $request->all());
        $customMessages = [
            'topicEn.required' => 'The topic in English field is required.',
            'topicSi.required' => 'The topic in Sinhala field is required.',
            'topicTa.required' => 'The topic in Tamil field is required.',
            'image_*.*.required' => 'Please upload at least one image.',
            'image_*.*.image' => 'The file must be an image.',
            'image_*.*.mimes' => 'The image must be a JPEG file.',
            'image_*.*.max' => 'The image must be less than 10MB in size.',
        ];

        $rules = [
            'topicEn' => 'required',
            'topicSi' => 'required',
            'topicTa' => 'required',
        ];

        // Dynamically add rules for each image
        foreach ($request->file() as $key => $files) {
            $rules[$key . '.*'] = 'required|image|mimes:jpeg,jpg|max:10240'; // Validate each image separately
        }


        $validator = Validator::make($request->all(), $rules, $customMessages);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['errors' => $errors], 422);
        } else {
            $response = $this->repository->addGallery($request);
            return response($response, 201);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Gallery  $gallery
     * @return \Illuminate\Http\Response
     */
    public function show(Gallery $gallery)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Gallery  $gallery
     * @return \Illuminate\Http\Response
     */
    public function edit(Gallery $gallery)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Gallery  $gallery
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
//        \Log::info('Data received for update acts:', $request->all());

        $customMessages = [
            'topicEn.required' => 'The topic in English field is required.',
            'topicSi.required' => 'The topic in Sinhala field is required.',
            'topicTa.required' => 'The topic in Tamil field is required.',
            'image_*.*.required' => 'Please upload at least one image.',
            'image_*.*.image' => 'The file must be an image.',
            'image_*.*.mimes' => 'The image must be a JPEG file.',
            'image_*.*.max' => 'The image must be less than 10MB in size.',
        ];

        $rules = [
            'topicEn' => 'required',
            'topicSi' => 'required',
            'topicTa' => 'required',
        ];

        // Dynamically add rules for each image
        foreach ($request->file() as $key => $files) {
            $rules[$key . '.*'] = 'required|image|mimes:jpeg,jpg|max:10240'; // Validate each image separately
        }


        $validator = Validator::make($request->all(), $rules, $customMessages);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['errors' => $errors], 422);
        } else {

            $response = $this->repository->updateGallery($id, $request);

            return response()->json($response, 200);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Gallery  $gallery
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $response = $this->repository->deleteGallery($id);

        if ($response->status() === 204) {
            return response()->json(['message' => 'Gallery deleted successfully.'], 200);
        } elseif ($response->status() === 404) {
            return response()->json(['error' => 'Gallery not found.'], 404);
        } else {
            return response()->json(['error' => 'Error deleting gallery.'], 500);
        }
    }
}
