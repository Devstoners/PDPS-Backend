<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Repositories\GalleryRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GalleryController extends Controller
{
    private $repository;

    public function __construct(GalleryRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        return $this->repository->getAllGalleries();
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $customMessages = [
            'topicEn.required' => 'The Topic English is compulsory',
            'topicSi.required' => 'The Topic Sinhala is compulsory',
            'topicTa.required' => 'The Topic Tamil is compulsory',
            'imageList.required' => 'At least one image is required',
            'imageList.*.image' => 'Each file must be an image',
            'imageList.*.mimes' => 'Each file must be a JPEG image',
            'imageList.*.max' => 'Each image may not be greater than 10 MB',
        ];

        $validator = Validator::make($request->all(), [
            'topicEn' => 'required',
            'topicSi' => 'required',
            'topicTa' => 'required',
            'imageList' => 'required|array|min:1',
            'imageList.*' => 'required|image|mimes:jpeg|max:10240',
        ], $customMessages);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['errors' => $errors], 422);
        } else {
            return $this->repository->createGallery($request);
        }
    }

    public function show($id)
    {
        $gallery = Gallery::with('images')->find($id);
        if (!$gallery) {
            return response()->json(['error' => 'Gallery not found'], 404);
        }
        return response()->json($gallery);
    }

    public function edit(Gallery $gallery)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $customMessages = [
            'topicEn.required' => 'The Topic English is compulsory',
            'topicSi.required' => 'The Topic Sinhala is compulsory',
            'topicTa.required' => 'The Topic Tamil is compulsory',
        ];

        $baseRules = [
            'topicEn' => 'required',
            'topicSi' => 'required',
            'topicTa' => 'required',
        ];

        if ($request->hasFile('imageList')) {
            $customMessages['imageList.*.image'] = 'Each file must be an image';
            $customMessages['imageList.*.mimes'] = 'Each file must be a JPEG image';
            $customMessages['imageList.*.max'] = 'Each image may not be greater than 10 MB';

            $baseRules['imageList'] = 'array|min:1';
            $baseRules['imageList.*'] = 'image|mimes:jpeg|max:10240';
        }

        $validator = Validator::make($request->all(), $baseRules, $customMessages);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['errors' => $errors], 422);
        } else {
            return $this->repository->updateGallery($id, $request);
        }
    }

    public function destroy($id)
    {
        $result = $this->repository->deleteGallery($id);

        if ($result) {
            return response()->json(['message' => 'Gallery deleted successfully.']);
        }
        return response()->json(['message' => 'Gallery not found.'], 404);
    }
}
