<?php

namespace App\Http\Controllers;

use App\Models\WaterSupply;
use App\Repositories\Repository;
use Illuminate\Http\Request;

class WaterSupplyController extends Controller
{
    private $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return WaterSupply::all();

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
        $fields = $request->validate([
            'aname' => 'required',
            'address' => 'required',
            'password' => 'string',
            'tele' => 'required',
            'distance' => 'required',
            'taxNo' => 'required',
            'email' => 'required',
            'scheme' => 'required',
            'service' => 'required',
        ]);
        $responce = $this->repository->addNewWater($fields);
        return response($responce, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(WaterSupply $waterSupply)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WaterSupply $waterSupply)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WaterSupply $waterSupply)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WaterSupply $waterSupply)
    {
        //
    }
}
