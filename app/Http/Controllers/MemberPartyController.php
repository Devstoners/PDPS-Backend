<?php

namespace App\Http\Controllers;

use App\Models\MemberParty;
use App\Repositories\MemberRepository;
use Illuminate\Http\Request;

class MemberPartyController extends Controller
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
    public function index()
    {
        $party = MemberParty::select('id', 'party_en','party_si','party_ta')->get();
        $response = [
            "AllParties" => $party,
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
            'partyEn' => 'required',
            'partySi' => 'required',
            'partyTa' => 'required',
        ]);
        $responce = $this->repository->addParty($request);
        return response($responce, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MemberParty  $memberParty
     * @return \Illuminate\Http\Response
     */
    public function show(MemberParty $memberParty)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MemberParty  $memberParty
     * @return \Illuminate\Http\Response
     */
    public function edit(MemberParty $memberParty)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MemberParty  $memberParty
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MemberParty $memberParty)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MemberParty  $memberParty
     * @return \Illuminate\Http\Response
     */
    public function destroy(MemberParty $memberParty)
    {
        //
    }
}
