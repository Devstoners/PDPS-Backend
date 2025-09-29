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

        $positions = OfficerPosition::with([
            'level' => function ($query) {
                $query->select('id', 'level_en');
            },

            'service' => function ($query) {
                $query->select('id', 'sname_en');
            },
        ])
            ->select('id', 'position_en','position_si','position_ta','officer_services_id','officer_levels_id')
            ->get();

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
            'postEn' => 'The Post English is compulsory',
            'postSi' => 'The Post Sinhala is compulsory',
            'postTa' => 'The Post Tamil is compulsory',
        ];

        $validator = Validator::make($request->all(),[
            'postEn' => 'required',
            'postSi' => 'required',
            'postTa' => 'required',
        ], $customMessages);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['errors' => $errors], 422);
        }else{
            $response = $this->repository->addPosition($request);
            return response($response, 201);
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
        $customMessages = [
            'postEn' => 'The Post English is compulsory yakooo',
            'postSi' => 'The Post Sinhala is compulsory',
            'postTa' => 'The Post Tamil is compulsory',
        ];

        $validator = Validator::make($request->all(),[
            'postEn' => 'required',
            'postSi' => 'required',
            'postTa' => 'required',
        ], $customMessages);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['errors' => $errors], 422);
        }else{
            $response = $this->repository->updatePosition($id, $request);
            return response($response, 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\OfficerPosition  $officerPosition
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $response = $this->repository->deletePosition($id);

        if ($response->status() === 204) {
            return response()->json(['message' => 'Post deleted successfully.'], 200);
        } elseif ($response->status() === 404) {
            return response()->json(['error' => 'Post not found.'], 404);
        } else {
            return response()->json(['error' => 'Error deleting Post.'], 500); // Or any other appropriate status code
        }
    }

    public function getPositionsByService($serviceId) {
        $positions = OfficerPosition::where('officer_services_id', $serviceId)->get();
        return response()->json($positions);
    }
}
