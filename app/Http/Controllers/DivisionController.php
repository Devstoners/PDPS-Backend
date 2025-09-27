<?php

namespace App\Http\Controllers;

use App\Models\Division;
use Illuminate\Http\Request;
use App\Repositories\MemberRepository;
use OpenApi\Annotations as OA;

class DivisionController extends Controller
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
    /**
     * @OA\Get(
     *     path="/division",
     *     tags={"Divisions"},
     *     summary="Get all divisions",
     *     description="Retrieve a list of all divisions",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Divisions retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="AllDivisions", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="division_en", type="string", example="Division 1"),
     *                 @OA\Property(property="division_si", type="string", example="විධාන 1"),
     *                 @OA\Property(property="division_ta", type="string", example="பிரிவு 1")
     *             ))
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function index()
    {
        $division = Division::select('id', 'division_en','division_si','division_ta')->get();
        $response = [
            "AllDivisions" => $division,
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
            'divisionEn' => 'required',
            'divisionSi' => 'required',
            'divisionTa' => 'required',
        ]);
        $responce = $this->repository->addDivision($request);
        return response($responce, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Division  $memberDivision
     * @return \Illuminate\Http\Response
     */
    public function show(Division $memberDivision)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Division  $memberDivision
     * @return \Illuminate\Http\Response
     */
    public function edit(Division $memberDivision)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Division  $memberDivision
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'divisionEn' => 'required',
            'divisionSi' => 'required',
            'divisionTa' => 'required',
        ]);

        $response = $this->repository->updateDivision($id, $request);

        return response($response, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Division  $memberDivision
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = $this->repository->deleteDivision($id);

        if ($result) {
            return response()->json(['message' => 'Division deleted successfully.']);
        }
        return response()->json(['message' => 'Division not found.'], 404);
    }
}
