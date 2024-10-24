<?php

namespace App\Http\Controllers;

use App\Models\Complain;
use App\Repositories\ComplainRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ComplainController extends Controller
{
    private $repository;

    public function __construct(ComplainRepository $repository)
    {
        $this->repository = $repository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->repository->getComplain();
        // return Complain::all();

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $customMessages = [
            'tele.size' => 'The Telephone number must be 10 digits',
            'complain.required' => 'The complain is compulsory',
            'complain.max' => 'The complain must be maximum of 1000 characters',
            'imageList.image' => 'Each file must be an image file',
            'imageList.mimes' => 'Each file must be a JPEG image',
            'imageList.max' => 'Each image may not be greater than 10 MB',
            'imageList.array' => 'You can only upload a maximum of 3 images',
            'imageList.*.max' => 'Each image may not be greater than 10 MB', // For individual file size
        ];

        $baseRules = [
            'complain' => 'required|max:1000',
            'tele' => 'size:10',
        ];

        if ($request->has('imageList') && $request->file('imageList') !== null) {
            $baseRules['imageList'] = 'array|max:3'; // Limit to a maximum of 3 images
            $baseRules['imageList.*'] = 'image|mimes:jpeg|max:10240'; // Each image should be jpeg and max 10MB
        }

        $validator = Validator::make($request->all(), $baseRules, $customMessages);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['errors' => $errors], 422);
        } else {
            $response = $this->repository->addComplain($request);
            return response($response, 201);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Complain $Complain)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Complain $Complain)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $result = $this->repository->deleteComplain($id);

        if ($result) {
            return response()->json(['message' => 'Complain deleted successfully.']);
        }
        return response()->json(['message' => 'Complain not found.'], 404);
    }

    public function getCount()
    {
        $count = Complain::count();
        $response = [
            "count" => $count,
        ];
        return response($response, 200);
    }
}
