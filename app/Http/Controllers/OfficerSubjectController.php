<?php

namespace App\Http\Controllers;


use App\Models\OfficerSubject;
use App\Models\OfficerLevel;
use App\Models\OfficerPosition;
use Illuminate\Http\Request;
use App\Repositories\OfficerRepository;
use Illuminate\Support\Facades\Validator;

class OfficerSubjectController extends Controller
{

    private $repository;
    public function __construct(OfficerRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $subject = OfficerSubject::select('id', 'subject_en','subject_si','subject_ta')->get();
        // $response = [
        //     "AllSubjects" => $subject,
        // ];
        // return response($response, 200);

        $subjects = OfficerSubject::with([
            'level' => function ($query) {
                $query->select('id', 'level_en');
            },
        ])
            ->select('id', 'subject_en','subject_si','subject_ta','officer_levels_id')
            ->get();

        $response = [
            "AllSubjects" => $subjects,
        ];
        return response($response, 200);

//        return OfficerSubject::all();
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
        $customMessages = [
            'dutyEn' => 'The Subject English is compulsory',
            'dutySi' => 'The Subject Sinhala is compulsory',
            'dutyTa' => 'The Subject Tamil is compulsory',
        ];

        $validator = Validator::make($request->all(),[
            'dutyEn' => 'required',
            'dutySi' => 'required',
            'dutyTa' => 'required',
        ], $customMessages);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['errors' => $errors], 422);
        }else{
            $response = $this->repository->addSubject($request);
            return response($response, 201);
        }
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
    public function update(Request $request, $id)
    {
        $customMessages = [
            'dutyEn' => 'The Subject English is compulsory',
            'dutySi' => 'The Subject Sinhala is compulsory',
            'dutyTa' => 'The Subject Tamil is compulsory',
        ];

        $validator = Validator::make($request->all(),[
            'dutyEn' => 'required',
            'dutySi' => 'required',
            'dutyTa' => 'required',
        ], $customMessages);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['errors' => $errors], 422);
        }else{
            $response = $this->repository->updateSubject($id, $request);
            return response($response, 200);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\OfficerSubject  $officerSubject
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $response = $this->repository->deleteSubject($id);

        if ($response->status() === 204) {
            return response()->json(['message' => 'Subject deleted successfully.'], 200);
        } elseif ($response->status() === 404) {
            return response()->json(['error' => 'Subject not found.'], 404);
        } else {
            return response()->json(['error' => 'Error deleting subject.'], 500); // Or any other appropriate status code
        }
    }

    public function getDutiesByPosition($positionId) {
        // Fetch the OfficerPosition by ID
        $position = OfficerPosition::find($positionId);

        if (!$position) {
            return response()->json(['error' => 'Position not found'], 404);
        }

        // Get the OfficerLevel related to the position using officer_levels_id
        $level = $position->level; // Assumes the OfficerPosition model has a relationship defined

        // Fetch the OfficerSubjects (Duties) related to the OfficerLevel using officer_levels_id
        $duties = OfficerSubject::where('officer_levels_id', $level->id)->get();

        return response()->json($duties);
    }


}
