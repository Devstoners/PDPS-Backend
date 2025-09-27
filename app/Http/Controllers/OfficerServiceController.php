<?php

namespace App\Http\Controllers;

use App\Models\OfficerService;
use App\Http\Requests\StoreOfficerServiceRequest;
use App\Http\Requests\UpdateOfficerServiceRequest;

class OfficerServiceController extends Controller
{
    /**
     * Display a listing of the resource.
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
