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
        $members = $this->repository->getMembers();
        return response($members, 200);
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
//            'email' => 'required|email|unique:users,email',
//            'name_en' => 'required|max:250',
//            'name_si' => 'required|max:250',
//            'name_ta' => 'required|max:250',
//            'image' => 'required',
//            'gender' => 'required',
//            'nic' => 'required|size:12',
//            'tel' => 'required|size:10',
//            'address' => 'required|max:250',
//            'is_married' => 'required|boolean',
//            'member_divisions_id' => 'required|integer',
//            'member_parties_id' => 'required|integer',
        ];

//        $validator = Validator::make($request->all(), $rules);
//
//        if ($validator->fails()) {
//            return redirect()->back()
//                ->withErrors($validator)
//                ->withInput();
//        }

        try {
            $response = $this->repository->createMember($request->all());
            return response()->json($response, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
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
    public function destroy(Member $member)
    {
        //
    }
}
