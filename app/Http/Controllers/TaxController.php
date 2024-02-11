<?php

namespace App\Http\Controllers;

use App\Models\tax;
use App\Repositories\Repository;
use Illuminate\Http\Request;

class TaxController extends Controller
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
        //
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
            'name' => 'required',
            'nic' => 'required',
            'ammount' => 'string',
            'account' => 'required',
            "payment_type" => "required",
            "address" => "required",
            "year" => "required",
            "telephone" => "required",
            "description" => "required"

        ]);
        $responce = $this->repository->addNewTax($fields);
        return response($responce, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(tax $tax)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(tax $tax)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, tax $tax)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(tax $tax)
    {
        //
    }
}
