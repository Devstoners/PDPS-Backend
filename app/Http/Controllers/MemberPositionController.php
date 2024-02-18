<?php

namespace App\Http\Controllers;

use App\Models\MemberPosition;
use App\Repositories\MemberRepository;
use Illuminate\Http\Request;

class MemberPositionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $repository;
    public function __construct(MemberRepository $repository)
    {
        $this->repository = $repository;
    }
    public function index()
    {
        $positions = MemberPosition::select('id', 'position_en')->get();
        $response = [
            "AllPositions" => $positions,
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MemberPosition  $memberPosition
     * @return \Illuminate\Http\Response
     */
    public function show(MemberPosition $memberPosition)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MemberPosition  $memberPosition
     * @return \Illuminate\Http\Response
     */
    public function edit(MemberPosition $memberPosition)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MemberPosition  $memberPosition
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MemberPosition $memberPosition)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MemberPosition  $memberPosition
     * @return \Illuminate\Http\Response
     */
    public function destroy(MemberPosition $memberPosition)
    {
        //
    }
}
