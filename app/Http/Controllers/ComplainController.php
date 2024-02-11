<?php

namespace App\Http\Controllers;

use App\Models\Complain;
use App\Repositories\ComplainRepository;
use Illuminate\Http\Request;

class ComplainController extends Controller
{
    private $repository;

    public function __construct(ComplainRepository $repository)
    {
        $this->repository = $repository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Complain::all();

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
        //return($request);
        $fields = $request->validate([
            'complain' => 'required',
            //'complain_date' => 'required',
            'cname' => 'string',
        ]);

        $responce = $this->repository->addComplain($request);
        return response($responce, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Complain $Complain)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Complain $Complain)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Complain $Complain)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Complain $Complain)
    {
        //
    }
    public function getCount()
    {
        $count = Complain::count();
        $response = [
            "count" => $count,
        ];
        return response($response, 200);
    }
}
