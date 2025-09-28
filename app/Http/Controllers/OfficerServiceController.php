<?php

namespace App\Http\Controllers;

use App\Models\OfficerService;
use App\Http\Requests\StoreOfficerServiceRequest;
use App\Http\Requests\UpdateOfficerServiceRequest;
use OpenApi\Annotations as OA;

class OfficerServiceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/officerServices",
     *     tags={"Officers"},
     *     summary="Get all officer services",
     *     description="Retrieve a list of all officer services for dropdowns and directory views",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Officer services retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="AllServices",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="sname_en", type="string", example="Administrative Service"),
     *                     @OA\Property(property="sname_si", type="string", example="පරිපාලන සේවය"),
     *                     @OA\Property(property="sname_ta", type="string", example="நிர்வாக சேவை")
     *                 )
     *             )
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
        $service = OfficerService::select('id', 'sname_en','sname_si','sname_ta')->get();
        $response = [
            "AllServices" => $service,
        ];
        return response($response, 200);
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
    public function store(StoreOfficerServiceRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(OfficerService $officerService)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OfficerService $officerService)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOfficerServiceRequest $request, OfficerService $officerService)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OfficerService $officerService)
    {
        //
    }
}
