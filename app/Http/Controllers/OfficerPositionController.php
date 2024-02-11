<?php

namespace App\Http\Controllers;
use App\Repositories\OfficerRepository;
use App\Models\OfficerPosition;
use Illuminate\Http\Request;

class OfficerPositionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */



    public function __construct(OfficerRepository $OfficerRepository)
    {
        $this->OfficerRepository = $OfficerRepository;

    }

    public function index()
    {
        return OfficerPosition::all();
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
        $fields = $request->validate([
            'position' => 'required',
        ]);

        $responce = $this->OfficerRepository->addPosition($request);
        return response($responce, 201);
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
    public function update(Request $request, OfficerPosition $officerPosition)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\OfficerPosition  $officerPosition
     * @return \Illuminate\Http\Response
     */
    public function destroy(OfficerPosition $officerPosition)
    {
        //
    }
}
