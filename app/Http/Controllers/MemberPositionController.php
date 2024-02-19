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
        $positions = MemberPosition::select('id', 'position_en','position_si','position_ta')->get();
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
        $request = $request->validate([
            'positionEn' => 'required',
            'positionSi' => 'required',
            'positionTa' => 'required',
        ]);
        $responce = $this->repository->addPosition($request);
        return response($responce, 201);
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
    public function update(Request $request, $id)
    {
        $request->validate([
            'positionEn' => 'required',
            'positionSi' => 'required',
            'positionTa' => 'required',
        ]);

        $response = $this->repository->updatePosition($id, $request);

        return response($response, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MemberPosition  $memberPosition
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = $this->repository->deletePosition($id);

        if ($result) {
            return response()->json(['message' => 'Position deleted successfully.']);
        }
        return response()->json(['message' => 'Position not found.'], 404);
    }
}
