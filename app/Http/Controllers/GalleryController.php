<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Models\GalleryImage;
use Illuminate\Http\Request;
use App\Repositories\AlbumGalleryRepository;
use Illuminate\Support\Facades\Validator;


class GalleryController extends Controller
{
    private $repository;
    public function __construct(AlbumGalleryRepository $repository)
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
        return $this->repository->getAllGalleries();
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
            return $this->repository->createGallery($request);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Gallery  $gallery
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->repository->getGalleryById($id);
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
        $customMessages = [
            'topicEn.required' => 'The topic in English field is required.',
            'topicSi.required' => 'The topic in Sinhala field is required.',
            'topicTa.required' => 'The topic in Tamil field is required.',
        ];

        $rules = [
            'topicEn' => 'required',
            'topicSi' => 'required',
            'topicTa' => 'required',
        ];

        // Add validation for new images if provided
        foreach ($request->file() as $key => $file) {
            if (strpos($key, 'new_image_') === 0) {
                $rules[$key] = 'image|mimes:jpeg,jpg|max:10240';
            }
        }

        $validator = Validator::make($request->all(), $rules, $customMessages);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['errors' => $errors], 422);
        } else {
            return $this->repository->updateGallery($id, $request);
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
        return $this->repository->deleteGallery($id);
    }
}
