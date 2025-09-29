<?php

namespace App\Http\Controllers;

use App\Models\OfficerLevel;
use App\Http\Requests\StoreOfficerLevelRequest;
use App\Http\Requests\UpdateOfficerLevelRequest;

class OfficerLevelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $level = OfficerLevel::select('id', 'level_en','level_si','level_ta')->get();
        $response = [
            "AllLevels" => $level,
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
    public function store(StoreOfficerLevelRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(OfficerLevel $officerLevel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OfficerLevel $officerLevel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOfficerLevelRequest $request, OfficerLevel $officerLevel)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OfficerLevel $officerLevel)
    {
        //
    }
}
