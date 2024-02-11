<?php

namespace App\Http\Controllers;

use App\Models\OfficersOfficerSubject;
use App\Models\OfficerSubject;
use Illuminate\Http\Request;
use App\Repositories\OfficerRepository;


class OfficerSubjectController extends Controller
{


    public function __construct(OfficerRepository $OfficerRepository)
    {
        $this->OfficerRepository = $OfficerRepository;

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return OfficerSubject::all();
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
            'subject' => 'required',
        ]);

        $response = $this->OfficerRepository->addSubject($request);
        return response($response, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\OfficerSubject  $officerSubject
     * @return \Illuminate\Http\Response
     */
    public function show(OfficerSubject $officerSubject)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\OfficerSubject  $officerSubject
     * @return \Illuminate\Http\Response
     */
    public function edit(OfficerSubject $officerSubject)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\OfficerSubject  $officerSubject
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, OfficerSubject $officerSubject)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\OfficerSubject  $officerSubject
     * @return \Illuminate\Http\Response
     */
    public function destroy(OfficerSubject $officerSubject)
    {
        //
    }
}
