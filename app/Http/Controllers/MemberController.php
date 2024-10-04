<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Http\Controllers\Controller;
use App\Repositories\MemberRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class MemberController extends Controller
{
    private $repository;

    public function __construct(MemberRepository $repository)
    {
        $this->repository = $repository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
//        return Member::all();
        return $this->repository->getMembers();

//        return response($members);
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
            'nameEn.required' => 'The Name English is compulsory',
            'nameSi.required' => 'The Name Sinhala is compulsory',
            'nameTa.required' => 'The Name Tamil is compulsory',
            // 'email.required' => 'The Email is compulsory',
            // 'email.email' => 'The Email must be a valid email address',
            // 'email.unique' => 'The Email has already been taken',
            'tel.required' => 'The Telephone number is compulsory',
            'tel.size' => 'The Telephone number must be 10 digits',
            'division.required' => 'The Division is compulsory',
            'party.required' => 'The Party is compulsory',
            'position.required' => 'The Position is compulsory',
            'position.array' => 'The Position is not an array',
        ];

        // Define the base validation rules
        $baseRules = [
            'nameEn' => 'required|max:250',
            'nameSi' => 'required|max:250',
            'nameTa' => 'required|max:250',
            // 'email' => 'required|email|unique:users,email',
            'tel' => 'required|size:10',
            'division' => 'required',
            'party' => 'required',
            'position' => 'required|array',
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
            $response = $this->repository->createMember($request);
            return response($response, 201);
        }
    }



    /**
     * Display the specified resource.
     */
    public function show(Member $member)
    {
//
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Member $member)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $customMessages = [
            'nameEn.required' => 'The Name English is compulsory',
            'nameSi.required' => 'The Name Sinhala is compulsory',
            'nameTa.required' => 'The Name Tamil is compulsory',
            'email.required' => 'The Email is compulsory',
            'email.email' => 'The Email must be a valid email address',
            // 'email.unique' => 'The Email has already been taken',
            'tel.required' => 'The Telephone number is compulsory',
            'tel.size' => 'The Telephone number must be 10 digits',
            'division.required' => 'The Division is compulsory',
            'party.required' => 'The Party is compulsory',
            'position.required' => 'The Position is compulsory',
            'position.array' => 'The Position is not an array',
        ];

        // Define the base validation rules
        $baseRules = [
            'nameEn' => 'required|max:250',
            'nameSi' => 'required|max:250',
            'nameTa' => 'required|max:250',
            'email' => 'required|email  ',
            'tel' => 'required|size:10',
            'division' => 'required',
            'party' => 'required',
            'position' => 'required|array',
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
            $response = $this->repository->updateMember($id, $request);
            return response($response, 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $result = $this->repository->deleteMember($id);

        if ($result) {
            return response()->json(['message' => 'Member deleted successfully.']);
        }
        return response()->json(['message' => 'Member not found.'], 404);
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
