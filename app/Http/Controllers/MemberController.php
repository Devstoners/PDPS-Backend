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
            'nameEn' => 'The Name English is compulsory',
            'nameSi' => 'The Name Sinhala is compulsory',
            'nameTa' => 'The Name Tamil is compulsory',
            'img.required' => 'The Image is compulsory',
            'img.image' => 'The Image must be an image file',
            'img.mimes' => 'The Image must be a JPEG file',
            'img.max' => 'The Image may not be greater than 5 MB',
            'email.required' => 'The Email is compulsory',
            'email.email' => 'The Email must be a valid email address',
            'email.unique' => 'The Email has already been taken',
            'tel.required' => 'The Telephone number is compulsory',
            'tel.size' => 'The Telephone number must be 10 digits',
            'division' => 'The Division is compulsory',
            'party' => 'The Party is compulsory',
            'position' => 'The Position is compulsory',
        ];

        $validator = Validator::make($request->all(),[
            'nameEn' => 'required|max:250',
            'nameSi' => 'required|max:250',
            'nameTa' => 'required|max:250',
            'email' => 'required|email|unique:users,email',
            'tel' => 'required|size:10',
            'division' => 'required',
            'party' => 'required',
            'position' => 'required|array',
            'img' => 'required|image|mimes:jpeg|max:5048',
        ], $customMessages);


        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['errors' => $errors], 422);
        }else{
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
    public function update(Request $request, Member $member)
    {
        //
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
}
