<?php

namespace App\Http\Controllers;

use App\Models\OfficerPosition;
use App\Repositories\OfficerRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OfficerPositionController extends Controller
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
        $positions = OfficerPosition::select('id', 'position_en','position_si','position_ta')->get();
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
        $customMessages = [
            'positionEn' => 'The Position English is compulsory',
            'positionSi' => 'The Position Sinhala is compulsory',
            'positionTa' => 'The Position Tamil is compulsory',
        ];

        $validator = Validator::make($request->all(),[
            'positionEn' => 'required|email',
            'positionSi' => 'required|email',
            'positionTa' => 'required',
        ], $customMessages);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['errors' => $errors], 422);
//            return response()->json(['errors' => $validator->errors()], 422);// with the field name
//            $errors = collect($validator->errors()->messages())->flatten()->toArray();//without the field name
//            return response()->json(['errors' => $errors], 422);
        }else{
            $responce = $this->repository->addPosition($request);
            return response($responce, 201);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\OfficerPosition  $officerPosition
     * @return \Illuminate\Http\Response
     */
    public function show(OfficerPosition $officerPosition)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\OfficerPosition  $officerPosition
     * @return \Illuminate\Http\Response
     */
    public function edit(OfficerPosition $officerPosition)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\OfficerPosition  $officerPosition
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
     * @param  \App\Models\OfficerPosition  $officerPosition
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
