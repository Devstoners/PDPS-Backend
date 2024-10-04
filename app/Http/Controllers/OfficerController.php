<?php

namespace App\Http\Controllers;

use App\Models\Officer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\OfficerRepository;
use Illuminate\Support\Facades\Validator;


class OfficerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $repository;

    public function __construct(OfficerRepository $repository)
    {
        $this->repository = $repository;
    }
    public function index()
    {
        // return Officer::all();
        return $this->repository->getOfficers();
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
        // \Log::info('xxxx: ' . $request);
        $customMessages = [
            'nameEn.required' => 'The Name English is compulsory',
            'nameSi.required' => 'The Name Sinhala is compulsory',
            'nameTa.required' => 'The Name Tamil is compulsory',
            'email.required' => 'The Email is compulsory',
            'email.email' => 'The Email must be a valid email address',
            'email.unique' => 'The Email has already been taken',
            'tel.required' => 'The Telephone number is compulsory',
            'tel.size' => 'The Telephone number must be 10 digits',
            'service.required' => 'The Service is compulsory',
            'grade.required' => 'The Grade is compulsory',
            'duty.array' => 'The Duty is compulsory',
            'status.required' => 'The Status is compulsory',
        ];

        // Define the base validation rules
        $baseRules = [
            'nameEn' => 'required|max:250',
            'nameSi' => 'required|max:250',
            'nameTa' => 'required|max:250',
            // 'email' => 'required|email|unique:users,email',
            'tel' => 'required|size:10',
            'service' => 'required',
            'grade' => 'required',
            'duty' => 'array',
        ];

        // If 'img' exists in the request, apply additional validation rules
        if ($request->has('img')&& $request->file('img') !== null) {
            $baseRules['img'] = 'image|mimes:jpeg,jpg,pjpeg,x-jpeg|max:10240';
            $customMessages['img.image'] = 'The Image must be an image file';
            $customMessages['img.mimes'] = 'The Image must be a JPEG file';
            $customMessages['img.max'] = 'The Image may not be greater than 5 MB';
        }

        $validator = Validator::make($request->all(), $baseRules, $customMessages);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['errors' => $errors], 422);
        } else {
            $response = $this->repository->addOfficer($request);
            return response($response, 201);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Officer  $officer
     * @return \Illuminate\Http\Response
     */
    public function show(Officer $officer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Officer  $officer
     * @return \Illuminate\Http\Response
     */
    public function edit(Officer $officer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Officer  $officer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $customMessages = [
            'nameEn.required' => 'The Name English is compulsory',
            'nameSi.required' => 'The Name Sinhala is compulsory',
            'nameTa.required' => 'The Name Tamil is compulsory',
            'email.required' => 'The Email is compulsory',
            'email.email' => 'The Email must be a valid email address',
            'email.unique' => 'The Email has already been taken',
            'tel.required' => 'The Telephone number is compulsory',
            'tel.size' => 'The Telephone number must be 10 digits',
            'service.required' => 'The Service is compulsory',
            'grade.required' => 'The Grade is compulsory',
            'duty.array' => 'The Duty is compulsory',
            'status.required' => 'The Status is compulsory',
        ];

        // Define the base validation rules
        $baseRules = [
            'nameEn' => 'required|max:250',
            'nameSi' => 'required|max:250',
            'nameTa' => 'required|max:250',
            // 'email' => 'required|email|unique:users,email',
            'tel' => 'required|size:10',
            'service' => 'required',
            'grade' => 'required',
            'duty' => 'array',
        ];

         // If 'img' exists in the request, apply additional validation rules
         if ($request->has('img')&& $request->file('img') !== null) {
            $baseRules['img'] = 'image|mimes:jpeg,jpg,pjpeg,x-jpeg|max:10240';
            $customMessages['img.image'] = 'The Image must be an image file';
            $customMessages['img.mimes'] = 'The Image must be a JPEG file';
            $customMessages['img.max'] = 'The Image may not be greater than 5 MB';
        }

        $validator = Validator::make($request->all(), $baseRules, $customMessages);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['errors' => $errors], 422);
        } else {
            $response = $this->repository->updateOfficer($id, $request);
            return response($response, 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Officer  $officer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = $this->repository->deleteOfficer($id);

        if ($result) {
            return response()->json(['message' => 'Officer deleted successfully.']);
        }
        return response()->json(['message' => 'Officer not found.'], 404);
    }

    public function count()
    {
        $count = $this->repository->getCount();
        $response = [
            "count" => $count,
        ];
        return response($response, 200);
    }
}
