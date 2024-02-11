<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Http\Controllers\Controller;
use App\Repositories\memberRepostory;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    private $repository;

    public function __construct(memberRepostory $repository)
    {
        $this->repository = $repository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $rules = [
            'email' => 'required|email|unique:users,email',
            'full_name' => 'required|max:250',
            'display_name' => 'required|max:100',
            'profile_pic' => 'required',
            'gender' => 'required',
            'nic' => 'required|size:12',
            'tel1' => 'required|size:10',
            'tel2' => 'required|size:10',
            'address' => 'required|max:250',
            'is_married' => 'required|boolean',
            'is_registered' => 'required|boolean',
            'member_divisions_id' => 'required|integer',
            'member_parties_id' => 'required|integer',
            'user_id' => 'required|integer',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $response = $this->repository->createMember($rules);
        return response($response, 201);
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
    public function destroy(Member $member)
    {
        //
    }
}
